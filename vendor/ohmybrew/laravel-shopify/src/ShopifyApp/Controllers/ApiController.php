<?php

namespace OhMyBrew\ShopifyApp\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OhMyBrew\ShopifyApp\Models\Shop;
use OhMyBrew\ShopifyApp\Facades\ShopifyApp;
use View;
use DB;
use Validator;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Batch;

class ApiController extends Controller
{
    public function UpdateLoacaLVarintStock($variant)
    {
        //  $variantUpdate=DB::table('tbl_product_inventory')->where('variant_id', $variantId['variant_id'])->update(['shopify_stock'=>$variant['stockInshopify']]);
    }

    public function CallStockey(Request $request)
    {
        $deliverdPO=[];
        $data=$this->curlForStockey();
        //echo '<pre>';
        //print_r($data);
        if ($data) {
            foreach ($data->purchase_orders as $po) {
                foreach ($po->purchase_items as $allPOItems) {
                    if ($allPOItems->status=="delivered") {
                        $podata=['poid'=>$po->id,'variant_poid'=>$allPOItems->id,'quantity'=>$allPOItems->quantity,'sku'=>$allPOItems->sku];
                        $checked=$this->checkStockyPO_alreadyProccessed($podata);
                        if ($checked) {
                            $deliverdPO[]=['sku'=>$allPOItems->sku, 'quantity'=>$allPOItems->quantity];
                        }
                    }
                }
            }
            if ($deliverdPO) {
                foreach ($deliverdPO as $processSku) {
                    $this->processForNewlyAdded($processSku['quantity'], '', $processSku['sku']);
                }
            }
        }
    }

    public function checkStockyPO_alreadyProccessed($purchaseOrder)
    {
        $check= DB::table('tbl_stocky_api_log')->where('poid', $purchaseOrder['poid'])->where('variant_poid', $purchaseOrder['variant_poid'])->first();
        if ($check) {
            return false;
        } else {
            $arrived=['poid'=>$purchaseOrder['poid'], 'variant_poid'=>$purchaseOrder['variant_poid'],'qty'=>$purchaseOrder['quantity'],'variant_sku'=>$purchaseOrder['sku']];
            $qtyset=DB::table('tbl_stocky_api_log')->insert($arrived);
            return true;
        }
    }
    public function getVariantIdBySKU($sku)
    {
        $check= DB::table('tbl_product_inventory')->where('sku', $sku)->first();
        if ($check) {
            return $check->variant_id;
        }
    }


    /*step1 for incoming order after succesfully checkout*/
    public function OrdersDetails(Request $request)
    {
        $oldOrder=$this->check_isDuplicateOrder($request->orderId);
        if (!$oldOrder) {
            $shopData=$this->getShopDetails($request->shop);
            $shop= ShopifyApp::shop_get($shopData->shopify_domain);
            $Orders=$shop->api()->request('GET', '/admin/orders/'.$request->orderId.'.json');
            $order=$Orders->body->order;
            foreach ($order->line_items as $products) {
                $dataall[]=['product_id'=>$products->product_id,'variant_id'=>$products->variant_id,'quantity'=>$products->quantity,'sku'=>$products->sku,'fulfillable_quantity'=>$products->fulfillable_quantity];
            }
            $orderProducts=['orderId'=>$order->id,'ordernum'=>$order->name,'productsDetails'=>$dataall];
            $this->MakeProcess_order($orderProducts, $request->shop);
        //  echo '<pre>';
          //  print_r($order);
          //  exit;
          //$this->ProcessOrder($orderProducts, $request->shop);
        } else {
            echo "already in";
        }
    }

    /* After order Process Order */
    public function MakeProcess_order($orderProducts, $shop)
    {
        $backorder=0;
        foreach ($orderProducts['productsDetails'] as $orderRequired) {
            $variantid=$orderRequired['variant_id'];
            $backorderData=$this->get_backorder_inventory_sum($variantid);
            $shopifyCurrentStock=  $this->getCurrent_quantity_in_shopify($shop, $variantid);
            $updateForLocal=['variant_id'=>$orderRequired['variant_id'],'stockInshopify'=>$shopifyCurrentStock];
            $this->UpdateLoacaLVarintStock($updateForLocal);
            if ($backorderData) {
                //  echo "In backorder". $backorderData;
                //exit;
                $backorder++;
                $localData=$this->get_stock_from_loacl($variantid);
                /*if already in backorder*/
                /*For untagging Backorder we need to check the quantity has updated by shopify or thirdparty app.*/
                //echo "Old back order :".$oldbackorderStock=$backorderData;
                $oldbackorderStock=$backorderData;
                //echo "<br>";
                //echo "shopify current stock :".$shopifyCurrentStock;
                //echo "<br>";
                //echo "This order requ :".$orderRequired['quantity'];
                $newOrderNegative=$orderRequired['fulfillable_quantity'];
                if ($shopifyCurrentStock <0) {
                    //echo "<br>";
                    //echo "shopify current stock:". $shopifyCurrentStock;
                    //echo "<br>";
                    //  exit;
                    $negativeStock_in_shopify=abs($shopifyCurrentStock);
                    //echo "<br>";
                    //echo "Negative in shopify= :".$negativeStock_in_shopify;
                    //exit;
                    $totalBCKORDERCURRENT=$oldbackorderStock+$newOrderNegative;
                    //echo "<br>";
                    //  echo "Total In Backorder: ".$totalBCKORDERCURRENT;

                    if ($negativeStock_in_shopify==$totalBCKORDERCURRENT) {
                        //  echo "<br>";
                        //  echo "No quan added= :".$negativeStock_in_shopify;

                        $backorderData=['order_id'=>$orderProducts['orderId'], 'product_id'=>$orderRequired['product_id'],'variant_id'=>$orderRequired['variant_id'],'inventory_item_id'=>'', 'sku'=>$orderRequired['sku'], 'order_required'=>$orderRequired['quantity'], 'backorder_stock'=>$orderRequired['fulfillable_quantity'], 'total_allocate'=>0,
                        'backorder_stock'=>$orderRequired['fulfillable_quantity'],'order_tag'=>'backorder','is_backorder'=>1,'ordernum'=>$orderProducts['ordernum']];
                        $this->entryForBackorder($backorderData);
                    } else {
                        $newlyAdded= $oldbackorderStock+$newOrderNegative-$negativeStock_in_shopify;

                        //  echo "<br>";
                        //echo "Newly added stock in shopify :".$newlyAdded;
                        //echo "<br>";
                        $backorderData=['order_id'=>$orderProducts['orderId'], 'product_id'=>$orderRequired['product_id'],'variant_id'=>$orderRequired['variant_id'],'inventory_item_id'=>'', 'sku'=>$orderRequired['sku'], 'order_required'=>$orderRequired['quantity'], 'backorder_stock'=>$orderRequired['quantity'], 'total_allocate'=>0,
                        'backorder_stock'=>$orderRequired['quantity'],'order_tag'=>'backorder','is_backorder'=>1,'ordernum'=>$orderProducts['ordernum']];
                        $this->entryForBackorder($backorderData);
                        $this->processForNewlyAdded($newlyAdded, $variantid);
                    }
                } else {
                    //echo "Shopify current stock not in negative : ".$shopifyCurrentStock;

                    $newlyAdded=  $this->Calculate_Newly_arrived($oldbackorderStock+$newOrderNegative, $shopifyCurrentStock);
                    $this->processForNewlyAdded($newlyAdded, $variantid);
                    $backorderData=['order_id'=>$orderProducts['orderId'], 'product_id'=>$orderRequired['product_id'],'variant_id'=>$orderRequired['variant_id'],'inventory_item_id'=>'', 'sku'=>$orderRequired['sku'], 'order_required'=>$orderRequired['quantity'], 'backorder_stock'=>0, 'total_allocate'=>$orderRequired['quantity'],
                    'backorder_stock'=>0,'order_tag'=>'ready_to_pick','is_backorder'=>0,'is_readytofulfill'=>1,'ordernum'=>$orderProducts['ordernum']];
                    $this->entryForBackorder($backorderData);
                }
            } else {
                /*if not in backorder*/

                $localData=$this->get_stock_from_loacl($variantid);
                $shopifyQty=$this->getCurrent_quantity_in_shopify($shop, $variantid);

                /*Not in backorder*/

                //echo "Not in backorder Shopify". $shopifyQty ;

                if ($shopifyQty >= 0) {

                    /*[Shopify have >=0 qty after order and no backorder] in this case there is no backorder for this  product variant then order tagged as (ready to pick) hence no need entry backorder in database*/
                    /*log*/
                    $requreTag[]=['tagtype'=>"readytofulfill",'orderid'=>$orderProducts['orderId'],'productid'=>$orderRequired['product_id'],'variantid'=>$orderRequired['variant_id'], 'sku'=>$orderRequired['sku']];
                    $backorderData=['order_id'=>$orderProducts['orderId'], 'product_id'=>$orderRequired['product_id'],'variant_id'=>$orderRequired['variant_id'],'inventory_item_id'=>'', 'sku'=>$orderRequired['sku'], 'order_required'=>$orderRequired['quantity'], 'backorder_stock'=>0, 'total_allocate'=>$orderRequired['quantity'],
                      'backorder_stock'=>0,'order_tag'=>'ready_to_pick','is_backorder'=>0,'is_readytofulfill'=>1,'ordernum'=>$orderProducts['ordernum']];
                    $this->entryForBackorder($backorderData);
                } else {
                    $fulfillable_quantity=$shopifyQty;
                    //echo "fulfillable_quantity ". $fulfillable_quantity;
                    //  exit;
                    $backorder++;
                    $requreTag[]=['tagtype'=>"backorder",'orderid'=>$orderProducts['orderId'],'productid'=>$orderRequired['product_id'],'variantid'=>$orderRequired['variant_id'], 'sku'=>$orderRequired['sku'],'ActualREQUIRED'=>abs($fulfillable_quantity)];
                    $backorderData=['order_id'=>$orderProducts['orderId'], 'product_id'=>$orderRequired['product_id'],'variant_id'=>$orderRequired['variant_id'],'inventory_item_id'=>'', 'sku'=>$orderRequired['sku'], 'order_required'=>$orderRequired['quantity'], 'backorder_stock'=>abs($fulfillable_quantity), 'total_allocate'=>abs($shopifyQty)-$orderRequired['quantity'],
                    'backorder_stock'=>abs($fulfillable_quantity),'order_tag'=>'backorder','is_backorder'=>1,'ordernum'=>$orderProducts['ordernum']];
                    $this->entryForBackorder($backorderData);

                    /*[Shopify have negative qty after order and no backorder] in this case there is no backorder for this  product variant then order tagged as (back order because shopify did not have sufficient_qty) hence  need entry backorder in database*/
                }
            }
        }

        $this->tagOrder($orderProducts['orderId']);

        //  echo '<pre>';
        //  print_r($requreTag);
    }



    /* process for newly added variants*/
    public function processForNewlyAdded($newlyAdded, $variantId=null, $sku=null)
    {
        if ($newlyAdded>0) {
            if ($variantId=='' || $variantId==null) {
                $variantId=$this->getVariantIdBySKU($sku);
            }
            if ($variantId!='') {
                /*get old backorder for untagging by variant id or sku*/
                $backOrder=$this->get_all_backorder_by_variant($variantId);
                //dd($backOrder);
                //exit;
                if ($backOrder) {
                    foreach ($backOrder as $singleBackorder) {
                        if ($newlyAdded >= $singleBackorder->backorder_stock) {
                            //  echo "Newly added larger than backorderr needed newly added :".$newlyAdded." In backorder:".$singleBackorder->backorder_stock;
                            if ($newlyAdded > 0) {
                                $newlyAdded=$this->Untagging_backOrders($variantId, $singleBackorder->order_id, $newlyAdded, $singleBackorder->backorder_stock);
                            }
                        } else {
                            if ($newlyAdded > 0) {
                                $newlyAdded=$this->Untagging_backOrders($variantId, $singleBackorder->order_id, $newlyAdded, $singleBackorder->backorder_stock);
                            }
                        }
                    }
                }
            }
        }
    }

    public function Untagging_backOrders($variantId, $orderId, $newlyAdded, $backorder)
    {
        /*in untagging process allocate to order and untag order first, then if remain newly added then return to untag other orders.*/
        if ($backorder > $newlyAdded) {
            $rmaining_in_samebacorder=$backorder-$newlyAdded;
            echo "<br>";
            echo "allocated for backorder :". $newlyAdded;
            $isUpadetForFullfill=null;
            echo "<br>";
            echo "Remaining in backorder :". $rmaining_in_samebacorder;
            $logmsg="Untagging processed for newly arrived stock Order: ".$orderId." Variant Id: ".$variantId." before update In backorder :".$backorder. " allocated :".$newlyAdded;
            $dtatUntag=['order_tag'=>'backorder','order_id'=>$orderId,'variant_id'=>$variantId,'total_allocate'=>$newlyAdded,'logmsg'=>$logmsg];

            $this->makeLOGRecord_process($dtatUntag);
            //
            $this->untagOrder($orderId, $variantId, $rmaining_in_samebacorder, $newlyAdded, $isUpadetForFullfill);
        } else {
            /*after $allocation if remain newly addded product return for next untagging process*/
            $rmaining_forNext_backorder=$newlyAdded-$backorder;
            echo "<br>";
            echo "allocated for backorder :". $backorder;
            $logmsg="Untagging processed for newly arrived stock Order: ".$orderId." Variant Id: ".$variantId." before update In backorder :".$backorder. " allocated :".$backorder;
            $dtatUntag=['order_tag'=>'backorder','order_id'=>$orderId,'variant_id'=>$variantId,'total_allocate'=>$backorder,'logmsg'=>$logmsg];
            $this->makeLOGRecord_process($dtatUntag);

            $this->untagOrder($orderId, $variantId, 0, $backorder, 1);
            echo "<br>";
            echo "Remaining in newly added :". $rmaining_forNext_backorder;
            echo "<br>";
            if ($rmaining_forNext_backorder>0) {
                return  $rmaining_forNext_backorder;
            }
        }
    }


    public function makeLOGRecord_process($backorderData)
    {
        $bckord= $this->get_variantDatOrderId($backorderData['variant_id'], $backorderData['order_id']);
        $dtat=[
            'orderid'=>$backorderData['order_id'],
            'ordernum'=>$bckord->ordernum,
            'productid'=>$bckord->product_id,
            'variantid'=>$backorderData['variant_id'],
            'sku'=>$bckord->sku,
            'logmsg'=>$backorderData['logmsg'],
            'logtype'=>$backorderData['order_tag'],
            'order_qty'=>$bckord->order_required,
            'avilable_qty'=>0,
            'previous_order_qty'=>$bckord->backorder_stock,
            'allocate_to_order'=>$backorderData['total_allocate']
            ];
        $insertLog=DB::table('tbl_log')->insert($dtat);
    }



    public function untagOrder($orderId, $variantId, $Currentbackorder_stock, $quantity_allocate, $isUpadetForFullfill)
    {
        $this->updateOrderDataInLocal($orderId, $variantId, $Currentbackorder_stock, $quantity_allocate, $isUpadetForFullfill);
        $this->tagOrder($orderId);
    }
    /*untag update in localDataBase*/
    public function updateOrderDataInLocal($orderId, $variantId, $Currentbackorder_stock, $quantity_allocate, $isUpadetForFullfill=null)
    {
        /*Upadete tbl_backorder_inventory */
        if ($isUpadetForFullfill!=null) {
            $BacOrderUp=['backorder_stock'=>$Currentbackorder_stock, 'order_tag'=>'readytofulfill', 'is_backorder'=>0,'is_readytofulfill'=>1];
        } else {
            $BacOrderUp=['backorder_stock'=>$Currentbackorder_stock];
        }

        $qtyset=DB::table('tbl_backorder_inventory')->where('order_id', $orderId)->where('variant_id', $variantId)->update($BacOrderUp);
        /*Upadete tbl_product_inventory */
        $Stockk=$this->get_stock_from_loacl($variantId);
        $qtysets=DB::table('tbl_product_inventory')->where('variant_id', $variantId)->update(['backorder_stock'=>$Stockk->backorder_stock-$quantity_allocate]);
    }
    public function tagOrder($orderId)
    {
        $requireBackorder=0;
        $orderInventory=$this->get_all_current_orderDetails($orderId);
        //dd($orderInventory);
        //exit;
        foreach ($orderInventory as $singleInventory) {
            if ($singleInventory->backorder_stock >0) {
                if ($singleInventory->is_backorder ==1) {
                    $requireBackorder++;
                    $tag_data[]="backorder: ".$singleInventory->sku.":".$singleInventory->backorder_stock;
                }
            }
        }
        if ($requireBackorder > 0) {
            $tag= "backorder,". implode(", ", $tag_data);
        } else {
            $tag= "ready_to_pick";
        }
        //print_r($tag);
        //exit;
        $this->updateOrderTag($tag, $orderId, "test-curvy.myshopify.com");
    }




    /* calculate newly Arrived qty*/
    public function Calculate_Newly_arrived($total_backorder, $currentInShopify)
    {
        return $total_backorder+$currentInShopify;
    }


    /* check for order already processed */
    public function check_isDuplicateOrder($orderId)
    {
        return  DB::table('tbl_log')->where('orderid', $orderId)->first();
    }

    /*Get store details*/
    public function getShopDetails($shopp)
    {
        return  DB::table('shops')->where('shopify_domain', $shopp)->first();
    }
    /* get all Variants details by order Id*/
    public function get_all_current_orderDetails($orderId)
    {
        return $tbl_backorder_inventory = DB::table('tbl_backorder_inventory')->where('order_id', $orderId)->get();
    }





    public function entryForBackorder($backorderData)
    {
        /*tbl_backorder_inventory tbl_previous_order*/
        //print_r($backorderData);
        //exit;
        $qtyset=DB::table('tbl_backorder_inventory')->insert($backorderData);

        if ($qtyset) {
            $Stockk=$this->get_stock_from_loacl($backorderData['variant_id']);
            $backorderStck=$Stockk->backorder_stock;

            $qtyset=DB::table('tbl_product_inventory')->where('variant_id', $backorderData['variant_id'])->update(['backorder_stock'=>$backorderStck+$backorderData['backorder_stock']]);
            $this->makeLOGRecord($backorderData, $backorderStck);
        }
    }

    public function makeLOGRecord($backorderData, $backorderStck)
    {
        if ($backorderData['order_tag']=="backorder") {
            $logMsg="Order Tagged as Backorder : Order Number ".$backorderData['ordernum'].", Product Id :".$backorderData['product_id'].", Variant Id :".$backorderData['variant_id'].", Quantity :".$backorderData['order_required'].", sku :".$backorderData['sku'];
        } else {
            $logMsg="Order Tagged as ready to pick : Order Number ".$backorderData['ordernum'].", Product Id :".$backorderData['product_id'].", Variant Id :".$backorderData['variant_id'].", Quantity :".$backorderData['order_required'].", sku :".$backorderData['sku'];
        }

        $dtat=[
            'orderid'=>$backorderData['order_id'],
            'ordernum'=>$backorderData['ordernum'],
            'productid'=>$backorderData['product_id'],
            'variantid'=>$backorderData['variant_id'],
            'sku'=>$backorderData['sku'],
            'logmsg'=>$logMsg,
            'logtype'=>$backorderData['order_tag'],
            'order_qty'=>$backorderData['order_required'],
            'avilable_qty'=>0,
            'previous_order_qty'=>$backorderStck,
            'allocate_to_order'=>$backorderData['total_allocate']
            ];
        $insertLog=DB::table('tbl_log')->insert($dtat);
    }
    /* inventory details shopify, backorder quantity, sku */
    public function get_stock_from_loacl($variantId)
    {
        return $shopdata = DB::table('tbl_product_inventory')->where('variant_id', $variantId)->first();
    }
    /* get inventory sum for backorder */
    public function get_backorder_inventory_sum($variantId)
    {
        $shopdata = DB::table('tbl_product_inventory')->where('variant_id', $variantId)->first();
        if ($shopdata) {
            if ($shopdata->backorder_stock != 0) {
                return $shopdata->backorder_stock;
            }
        }
    }

    /* order inventory details shopify, backorder quantity, sku ,order_required,backorder_stock,total_allocate,status of variant*/
    public function get_backorder_by_variant($variantId)
    {
        return $shopdata = DB::table('tbl_backorder_inventory')->where('variant_id', $variantId)->first();
    }
    /*get all backorder by variants*/
    public function get_all_backorder_by_variant($variantId)
    {
        return $shopdata = DB::table('tbl_backorder_inventory')->where('variant_id', $variantId)->orderBy('id', 'asc')->get();
    }
    public function get_variantDatOrderId($variantId, $orderId)
    {
        return $shopdata = DB::table('tbl_backorder_inventory')->where('order_id', $orderId)->where('variant_id', $variantId)->first();
    }

    /*update order tags, for backorder and readytofulfill*/
    public function updateOrderTag($tag, $orderid, $shop)
    {
        $shopData=$this->getShopDetails($shop);
        $shops= ShopifyApp::shop_get($shopData->shopify_domain);
        $dttt=['id'=>$orderid,'tags'=>$tag];
        $Orders=$shops->api()->request('PUT', '/admin/orders/'.$orderid.'.json', ['order'=>$dttt]);
        //echo $tag;
        //exit;
        // code...
    }
    /*get Current quantity in shopify*/
    public function getCurrent_quantity_in_shopify($shop, $variantId)
    {
        $shopData=$this->getShopDetails($shop);
        $shops= ShopifyApp::shop_get($shopData->shopify_domain);
        $Variants=$shops->api()->request('GET', '/admin/variants/'.$variantId.'.json');

        return $Variants->body->variant->inventory_quantity;
    }
    //Record Log
    public function Record_log($logData)
    {
        $qtyset=DB::table('tbl_log')->insert($logData);
    }



    /***********************************************OLD CODE **************************************************/

    public function getUpdatedProduct(Request $request)
    {
        $shop=$request->shop;
    }
    public function getBackOrderProducTS()
    {
    }


    /*******************Get Actual Quantity deduct previous from shopify quantity*****************************/
    public function getActualQuantity($productId, $variantId)
    {
        $CurrentUsed=$this->getBackOrderQuantity($productId, $variantId);
        $AvilableInShopify=$this->getAvailableQuantity("test-curvy.myshopify.com", $productId, $variantId);
        //print_r($AvilableInShopify);
        //exit;
        if ($AvilableInShopify>0) {
            if ($CurrentUsed>0) {
                if ($CurrentUsed>=$AvilableInShopify) {
                    return 0;
                } else {
                    return $AvilableInShopify-$CurrentUsed;
                }
            } else {
                return $AvilableInShopify;
            }
        } else {
            return 0;
        }
    }


    /*******************Get Shopify quantity*****************************/
    public function getAvailableQuantity($shop, $productId, $variantId)
    {
        $shopData=$this->getShopDetails($shop);
        $shop= ShopifyApp::shop_get($shopData->shopify_domain);
        $Variants=$shop->api()->request('GET', '/admin/variants/'.$variantId.'.json');

        return $Variants->body->variant->inventory_quantity;
    }

    /***************Get howmany quantity required*****************/
    public function getRequiredQuantity($avilabel, $required)
    {
        if ($avilabel!=0) {
            if ($avilabel>=$required) {
                return null;
            } else {
                return $required-$avilabel;
            }
        } else {
            return $required;
        }
    }

    public function getBackOrderQuantity($productId, $variantId)
    {
        return $shopdata = DB::table('tbl_previous_order')->where('productid', $productId)->where('variantid', $variantId)->where('status', '0')->sum('quantity_to_fulfill');
        //print_r($shopdata);
        //exit;
    }

    public function get_allProductsInShopify(Request $request)
    {
        $shopData=$this->getShopDetails($request->shop);
        $shop= ShopifyApp::shop_get($shopData->shopify_domain);
        $Api_request=$shop->api()->request('GET', '/admin/products.json?limit=250&page='.$request->page);
        $products=$Api_request->body->products;
        if ($products) {
            foreach ($products as $product) {
                foreach ($product->variants as $variant) {
                    $qtyset=DB::table('tbl_product_inventory')->insert(['product_id'=>$variant->product_id,
                  'variant_id'=>$variant->id,
                  'inventory_item_id'=>$variant->inventory_item_id,
                  'sku'=>$variant->sku,
                  'shopify_stock'=>$variant->inventory_quantity,
                  'before_update_shopify_stock'=>$variant->inventory_quantity,
                  ]);
                }
            }
        }
    }
    public function curlForStockey()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
      CURLOPT_URL => "https://stockyapi.herokuapp.com/api/v2/purchase_orders.json?status=confirmed",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_TIMEOUT => 30000,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        'Authorization: API KEY=c971df4ab78349a8f816c9fd602eef83',
        'Content-Type: application/json',
        'Store-Name: test-curvy.myshopify.com'
      ),
      ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return  json_decode($response);
        }
    }
}
