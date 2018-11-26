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
    public function OrdersDetails(Request $request)
    {
        $oldOrder=$this->check_isDuplicateOrder($request->orderId);
        if (!$oldOrder) {
            $shopData=$this->getShopDetails($request->shop);
            $shop= ShopifyApp::shop_get($shopData->shopify_domain);
            $Orders=$shop->api()->request('GET', '/admin/orders/'.$request->orderId.'.json');

            $order=$Orders->body->order;
            foreach ($order->line_items as $products) {
                $dataall[]=['productId'=>$products->product_id,'variantId'=>$products->variant_id,'quantity_orderd'=>$products->quantity,'sku'=>$products->sku];
            }
            $orderProducts=['orderId'=>$order->id,'ordernum'=>$order->number,'productsDetails'=>$dataall];
            $this->ProcessOrder($orderProducts, $request->shop);
        }
    }

    //Order Process
    public function ProcessOrder($orderProducts, $shop)
    {
        foreach ($orderProducts['productsDetails'] as $orderRequired) {
            $require=0;
            $orderRequired['productId'];
            $orderRequired['variantId'];
            $orderRequired['quantity_orderd'];
            // $avilable=$this->getActualQuantity($orderRequired['productId'], $orderRequired['variantId']);
            // /*print_r($avilable);*/
            // $ActualREQUIRED=$this->getRequiredQuantity($avilable, $orderRequired['quantity_orderd']);
            // if ($ActualREQUIRED) {
            //     $require++;
            //     /* Make log for In Sufficient quntity for variant*/
            //     $requreTag[]=['tagtype'=>"backorder",'orderid'=>$orderProducts['orderId'],'productid'=>$orderRequired['productId'],'variantid'=>$orderRequired['variantId'], 'sku'=>$orderRequired['sku'],'ActualREQUIRED'=>$ActualREQUIRED];
            //     $previous_order_qty=0;
            //     $logging=['orderid'=>$orderProducts['orderId'],
            //   'ordernum'=>$orderProducts['ordernum'],
            //   'productid'=>$orderRequired['productId'],
            //   'variantid'=>$orderRequired['variantId'],
            //   'order_qty'=>$orderRequired['quantity_orderd'],
            //   'previous_order_qty'=>$previous_order_qty,
            //   'avilable_qty'=>0,
            //   'logtype'=>'insufficient_qty',
            //   'logmsg'=>"Insufficient quantity of product : Order Number ".$orderProducts['ordernum'].", Product Id :".$orderRequired['productId'].", Variant Id :".$orderRequired['variantId'].", Quantity :".$orderRequired['quantity_orderd']
            //   ];
            //     $allocate=$orderRequired['quantity_orderd']-$ActualREQUIRED;
            //     $this->createBackOrderQuantity($orderProducts['orderId'], $orderRequired['productId'], $orderRequired['variantId'], $orderRequired['quantity_orderd'], $allocate, $orderRequired['sku']);
            // } else {
            //     $requreTag[]=['tagtype'=>"readytofulfill",'orderid'=>$orderProducts['orderId'],'productid'=>$orderRequired['productId'],'variantid'=>$orderRequired['variantId'], 'sku'=>$orderRequired['sku']];
            //     /* Make log for Sufficient quntity for variant*/
            //     $previous_order_qty=0;
            //     $logging=['orderid'=>$orderProducts['orderId'],
            //   'ordernum'=>$orderProducts['ordernum'],
            //   'productid'=>$orderRequired['productId'],
            //   'variantid'=>$orderRequired['variantId'],
            //   'order_qty'=>$orderRequired['quantity_orderd'],
            //   'previous_order_qty'=>$previous_order_qty,
            //   'avilable_qty'=>0,
            //   'logtype'=>'sufficient_qty',
            //   'logmsg'=>"Sufficient quantity of product : Order Number ".$orderProducts['ordernum'].", Product Id :".$orderRequired['productId'].", Variant Id :".$orderRequired['variantId'].", Quantity :".$orderRequired['quantity_orderd']
            //   ];
            //     $this->createBackOrderQuantity($orderProducts['orderId'], $orderRequired['productId'], $orderRequired['variantId'], $orderRequired['quantity_orderd'], $orderRequired['quantity_orderd'], $orderRequired['sku']);
            // }
            // $this->Record_log($logging);
        }
        if ($require>0) {
            /*update Tag for backorder*/

            foreach ($requreTag as $tagss) {
                if (empty($tagss['sku'])) {
                    $sku=$tagss['variantid'];
                } else {
                    $sku=$tagss['sku'];
                }
                if (isset($tagss['ActualREQUIRED'])) {
                    $tagarray[]=$sku.": ".$tagss['ActualREQUIRED'];
                }
            }
            $tag= "backorder:". implode(" ", $tagarray);
            /* print_r($tag);*?
            } else {
                /*update order for ready to fulfill
                print_r("Ready to fulfill");
                print_r("Ready to fulfill");*/
            $tag="Ready to fulfill";
        }
        //  echo $tag;
        $this->updateOrderTag($tag, $orderProducts['orderId'], $shop);
    }

    public function createBackOrderQuantity($orderid, $productid, $variantId, $quantityFulfill, $allocate, $sku)
    {
        if ($allocate>0) {
            $allocate_status=1;
        } else {
            $allocate_status=0;
        }
        $qtyset=DB::table('tbl_previous_order')->insert(['orderid'=>$orderid,
      'productid'=>$productid,
      'variantid'=>$variantId,
      'quantity_to_fulfill'=>$quantityFulfill,'sku'=>$sku,'qty_allocate'=>$allocate,'allocate_status'=>$allocate_status]);
    }
    //Record Log
    public function Record_log($logData)
    {
        $qtyset=DB::table('tbl_log')->insert($logData);
    }
    /*******************Get Actual Quantity deduct previous from shopify quantity*****************************/
    public function getActualQuantity($productId, $variantId)
    {
        $CurrentUsed=$this->getBackOrderQuantity($productId, $variantId);
        $AvilableInShopify=$this->getAvailableQuantity("wesley-app.myshopify.com", $productId, $variantId);
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

    public function updateOrderTag($tag, $orderid, $shop)
    {
        $shopData=$this->getShopDetails($shop);
        $shop= ShopifyApp::shop_get($shopData->shopify_domain);
        $dttt=['id'=>$orderid,'tags'=>$tag];
        $Orders=$shop->api()->request('PUT', '/admin/orders/'.$orderid.'.json', ['order'=>$dttt]);
        // code...
    }

    public function check_isDuplicateOrder($orderId)
    {
        return  DB::table('tbl_log')->where('orderid', $orderId)->first();
    }






    public function PlaceDelivery(Request $request)
    {
        //$this->updateToken($request->shop);
        $shopData=$this->getShopDetails($request->shop);
        $shop= ShopifyApp::shop_get($shopData->shopify_domain);
        $Orders=$shop->api()->request('GET', '/admin/orders/'.$request->orderId.'.json');

        if (isset($Orders->body)) {
            if (isset($Orders->body->order)) {
                $order=$Orders->body->order;
                if ($order->shipping_address) {

                    //  $RecipientName=$order->shipping_address
                    $shipping_address=$order->shipping_address;
                    $RecipientName=$shipping_address->first_name." ".$shipping_address->last_name;
                    $DeliveryInstructions="Test";
                    $DestinationAddress1=$shipping_address->address1;
                    $DestinationAddress2=$shipping_address->address2;
                    $DestinationCity=$shipping_address->city;
                    $DestinationStateId=$shipping_address->province_code;
                    $DestinationZipcode=$shipping_address->zip;
                    $shops= $this->getShopDetails($request->shop);
                    $dtt=array('UserId' => $shops->ninja_api_userid,
                    'AuthToken' => $shops->ninja_api_token,
                    'RecipientName'=>$RecipientName,
                    'DeliveryInstructions'=>$DeliveryInstructions,
                    'DestinationAddress1'=>$DestinationAddress1,
                    'DestinationAddress2'=>$DestinationAddress2,
                    'DestinationCity'=>$DestinationCity,
                    'DestinationStateId'=>$DestinationStateId,
                    'DestinationZipcode'=>$DestinationZipcode);
                    echo json_encode($dtt);
                    exit;
                    if ($shops) {
                        print_r($shops);
                        if (isset($shops->ninja_api_userid)) {
                            $apiclient=new Client([
                              'base_uri' => "https://api-test.ninjadelivery.com",
                              'headers' => [
                                  'Accept' => 'application/json',
                                  'Content-Type' => 'application/x-www-form-urlencoded',
                              ],
                          ]);
                            $response = $apiclient->request('POST', '/api/request/PlaceExpressRequest', [
                        'json' => [
                            'UserId' => $shops->ninja_api_userid,
                            'AuthToken' => $shops->ninja_api_token,
                            'RecipientName'=>$RecipientName,
                            'DeliveryInstructions'=>$DeliveryInstructions,
                            'DestinationAddress1'=>$DestinationAddress1,
                            'DestinationAddress2'=>$DestinationAddress2,
                            'DestinationCity'=>$DestinationCity,
                            'DestinationStateId'=>$DestinationStateId,
                            'DestinationZipcode'=>$DestinationZipcode

                        ]
                    ]);
                            $responseData= json_decode($response->getBody());
                            print_r($responseData);
                        }
                    }
                }
            }
        }
    }


    // Ninja Api Authentication Login



    public function NinjaAuthLogin(Request $request)
    {
        $apiclient=new Client([
                'base_uri' => "http://webservices.smiffys.com",
                'headers' => [
                    'Content-Type' => 'text/xml',
                ],
            ]);
        $response = $apiclient->request('GET', '/services/products.asmx/GetFullDataSet?apiKey=65b4e835502c4e2877c83fa76e38a310&clientID=EF_PARTICA&LanguageCode=EN');

        $response = $response->getBody()->getContents();
        libxml_use_internal_errors(true);
        //$xml=simplexml_load_string($myXMLData); //or simplexml_load_file
        $xml = simplexml_load_string($response);
        foreach (libxml_get_errors() as $error) {
            print_r($error);
        }

        $json = json_encode($xml);
        //$jjs=utf8_encode($json);
        //  $array = json_decode($json, true);
        $productData=json_decode($json, true);

        $table = 'products';
        $index = 'product_code';

        foreach ($productData['Product'] as $productRow) {
            if (isset($productRow['ProductName'])) {
                $productName= $productRow['ProductName'];
            } else {
                $productName='';
            }

            if (isset($productRow['BrochureDescription'])) {
                $BrochureDescription= $productRow['BrochureDescription'];
            } else {
                $BrochureDescription='';
            }

            if (isset($productRow['RRP'])) {
                $RRP= $productRow['RRP'];
            } else {
                $RRP='';
            }

            if (isset($productRow['Price1'])) {
                $Price1= $productRow['Price1'];
            } else {
                $Price1='';
            }

            if (isset($productRow['Colour'])) {
                $Colour= $productRow['Colour'];
            } else {
                $Colour='';
            }

            if (isset($productRow['size'])) {
                $size= $productRow['size'];
            } else {
                $size='';
            }

            if (isset($productRow['ProductCode'])) {
                $ProductCode= $productRow['ProductCode'];
            } else {
                $ProductCode='';
            }

            if (isset($productRow['FrontShot'])) {
                $FrontShot= $productRow['FrontShot'];
            } else {
                $FrontShot='';
            }

            if (isset($productRow['StockQuantity'])) {
                $StockQuantity= $productRow['StockQuantity'];
            } else {
                $StockQuantity='';
            }

            if (isset($productRow['BarCode'])) {
                $BarCode= $productRow['BarCode'];
            } else {
                $BarCode='';
            }

            $qtyset=DB::table('products')->insert(['title'=>$productName,
            'body_html'=>$BrochureDescription,
            'product_type'=>'Imported',
            'handle'=>'',
            'tags'=>'',
            'price'=>$RRP,
            'sku'=>'',
            'compare_at_price'=>$Price1,
            'option1'=>'',
            'option2'=>$size,
            'option3'=>'',
            'product_code'=>$ProductCode,
            'taxable'=>'',
            'image'=>$FrontShot,
            'inventory_quantity'=>$StockQuantity,
            'weight'=>'',
            'weight_unit'=>'',
            'inventory_item_id'=>'',
             'barcode'=>$BarCode]);
            //dd($qtyset);
            //exit;
        }
        //$value=array($prdData);
        //  print_r($value);
        //$dds=Batch::insert($table, $cols, $value, '10000');
        //echo '<pre>';
        //print_r($dds);





        //print_r($response);
        //     $qtysetf=DB::table('shops')->where('shopify_domain', $request->shop)->update(['ninja_email'=>$request->email,'ninja_pass'=>$request->password]);
    //
    //     $apiclient=new Client([
    //           'base_uri' => "https://api-test.ninjadelivery.com",
    //           'headers' => [
    //               'Accept' => 'application/json',
    //               'Content-Type' => 'application/x-www-form-urlencoded',
    //           ],
    //       ]);
    //     $response = $apiclient->request('POST', '/api/authentication/login', [
    //     'json' => [
    //         'Email' => $request->email,
    //         'Password' => $request->password,
    //     ]
    // ]);
    //     $responseData= json_decode($response->getBody());
    //     if ($responseData->responsecode==200 && $responseData->UserId!='') {
    //         $qtyset=DB::table('shops')->where('shopify_domain', $request->shop)->update(['ninja_api_userid'=>$responseData->UserId,'ninja_api_token'=>$responseData->AuthenticationToken]);
    //         return response()->json(['status'=>'success','code'=>200,'msg' =>"Api Validated"]);
    //     } else {
    //         return response()->json(['status'=>'fail','code'=>100,'msg' =>"Invalid Credentials!"]);
    //     }
    }


    public function updateToken($shop)
    {
        $shopdata = DB::table('shops')->where('shopify_domain', $shop)->first();
        //dd($shopdata);
        //  exit;
        $apiclient=new Client([
              'base_uri' => "https://api-test.ninjadelivery.com",
              'headers' => [
                  'Accept' => 'application/json',
                  'Content-Type' => 'application/x-www-form-urlencoded',
              ],
          ]);
        $response = $apiclient->request('POST', '/api/authentication/login', [
        'json' => [
            'Email' => $shopdata->ninja_email,
            'Password' => $shopdata->ninja_pass,
        ]
    ]);
        $responseData= json_decode($response->getBody());
        if ($responseData->responsecode==200 && $responseData->UserId!='') {
            $qtyset=DB::table('shops')->where('shopify_domain', $shop)->update(['ninja_api_userid'=>$responseData->UserId,'ninja_api_token'=>$responseData->AuthenticationToken]);
            return true;
        }
    }

    public function NinjaPickupLocation(Request $request)
    {
        $data=json_decode($request->datastring);
        parse_str($data, $AddressPrm);
        $this->updateToken($request->shop);
        //print_r($AddressPrm);

        $shop= $this->getShopDetails($request->shop);
        if ($shop) {

            //exit;
            //print_r($shop);
            if (isset($shop->ninja_api_userid)) {
                $addressShip=$this->get_shipping_address($AddressPrm, $shop);
                $apiclient=new Client([
                    'base_uri' => "https://api-test.ninjadelivery.com",
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                ]);
                $response = $apiclient->request('POST', '/api/user/GetAvailablePickupAddresses', [
              'json' => [
                  'UserId' => $shop->ninja_api_userid,
                  'AuthToken' => $shop->ninja_api_token,
              ]
          ]);
                $responseData= json_decode($response->getBody());
                //print_r($responseData);
                if ($responseData->ResponseCode==200) {
                    return response()->json(['ResponseCode'=>200,'pickupData'=>$responseData,'DeliveryEstimate'=>$addressShip, 'contactData'=>$request->datastring]);
                } else {
                    return response()->json(['status'=>'fail','code'=>100,'msg' =>"Invalid shop!"]);
                }
            } else {
                return response()->json(['status'=>'fail','code'=>100,'msg' =>"Invalid shop!"]);
            }
        }
    }

    public function tOrderAll(Request $request)
    {
    }

    public function PendingOrderAll(Request $request)
    {
        $current = Carbon::now();
        $oldDate = $current->subDays(59)->format('Y-m-d');
        $shopData=$this->getShopDetails($request->shop);
        $shop= ShopifyApp::shop_get($shopData->shopify_domain);
        $countOrders=$shop->api()->request('GET', '/admin/orders/count.json?created_at_min='.$oldDate);
        if ($countOrders->body->count) {
            $limit=$_POST['length'];
            $data['orderCount']=$countOrders->body->count;
            $start=$_POST['start']/$limit;
            $page=$start+1;
            $orders=$shop->api()->request('GET', '/admin/orders.json?page='.$page.'&limit='.$limit);
            if (isset($orders->body->orders)) {
                $filterd=  count($orders->body->orders);
                //print_r($filterd);
                $nestedData=array();
                $reco=array();
                foreach ($orders->body->orders as $order) {
                    $nestedData[] = $order->name;
                    $nestedData[] = $order->email;
                    $nestedData[] = $order->total_price;
                    if ($order->note=="Same Day Delivery") {
                        $nestedData[]="Yes";
                    } else {
                        $nestedData[]="No";
                    }
                    if ($order->tags="ninja_pending") {
                        $nestedData[]="Ninja Shipping";
                    } else {
                        $nestedData[]="";
                    }
                    $nestedData[] = $order->note;
                    if ($order->note=="Same Day Delivery") {
                        $nestedData[] = '<input type ="button" class="ui-button ui-button--primary ui-title-bar__action btn" onclick="placeDelivery('.$order->id.')" value="Place Delivery">';
                    } else {
                        $nestedData[] ='';
                    }
                    $reco[] = $nestedData;
                    $nestedData=array();
                }
            }



            $json_data = array(
          "draw"            => intval($_POST['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
          "recordsTotal"    => intval($data['orderCount']),  // total number of records
          "recordsFiltered" => intval($filterd), // total number of records after searching, if there is no searching then totalFiltered = totalData
          "data"            =>$reco   // total data array
          );

            return response()->json($json_data);
        }
    }


    public function getShopDetails($shopp)
    {
        return  DB::table('shops')->where('shopify_domain', $shopp)->first();
    }

    public function get_shipping_address($shippingAdd, $shop)
    {
        //print_r($shippingAdd['checkout']['shipping_address']['address1']);
        //print_r($shippingAdd['checkout']['shipping_address']['city']);
        //print_r($shippingAdd['checkout']['shipping_address']['country']);
        //print_r($shippingAdd['checkout']['shipping_address']['province']);
        //print_r($shippingAdd['checkout']['shipping_address']['zip']);

        $apiclient=new Client([
            'base_uri' => "https://api-test.ninjadelivery.com",
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);
        $response = $apiclient->request('POST', '/api/request/DeliveryEstimate', [
      'json' => [
          'UserId' => $shop->ninja_api_userid,
          'AuthToken' => $shop->ninja_api_token,
          'PickupAddress'=>null,
          'DestinationAddress'=>$shippingAdd['checkout']['shipping_address']['address1'].",".$shippingAdd['checkout']['shipping_address']['city']." ".$shippingAdd['checkout']['shipping_address']['country'].",".$shippingAdd['checkout']['shipping_address']['province']." ".$shippingAdd['checkout']['shipping_address']['zip'],
          'DestinationZipCode'=>$shippingAdd['checkout']['shipping_address']['zip']
      ]
  ]);
        return  $responseData= json_decode($response->getBody());
    }

    public function createDraftOrder(Request $request)
    {
        $items=$request->datastring['items'];
        //print_r($item);
        if ($request->shipping=="PickupStore") {
            $note="Store Pickup StoreId: ".$request->pickupStoreId;
            //shipping_line
            $shipping=array('custom'=>true,'price'=>0.00,'title'=>$request->pickupStore.": ".$request->pickupStoreStr);
        } else {
            //shipping_line
            $val = preg_replace('/&#36;/', '', $request->shippingValue);
            $shipping=array('custom'=>true,'price'=>$val,'title'=>"Same day delivery.");
            $note="Same Day Delivery";
        }

        foreach ($items as $singleItem) {
            $prdData['variant_id']=$singleItem['variant_id'];
            if ($singleItem['properties']) {
                //    $prdData['properties']=$this->makePropertiesArray($singleItem['properties']);
            }
            $prdData['quantity']=$singleItem['quantity'];
            $item[]=$prdData;
        }

        //print_r($draftt);
        $contactData=json_decode($request->contactData);
        parse_str($contactData, $contactArr);
        $customerId=$this->makeContact($contactArr, $request->shop);
        //$draftt=array('draft_orders'=>array('line_items'=>$item,'customer'=>array('id'=>$customerId),'shipping_line'=>$shipping));

        $draft = array( 'line_items' => $item, 'customer' => array( 'id' => $customerId ),'shipping_line'=>$shipping,"use_customer_default_address"=> true,"tags"=>"ninja_pending","note"=>$note);
        //echo json_encode($draft);

        $shop= ShopifyApp::shop_get($request->shop);
        $shopdata=$shop->api()->request('POST', '/admin/draft_orders.json', ['draft_order'=>$draft]);

        return response()->json(['status'=>'success','code'=>200,'orderData' => $shopdata]);
    }

    public function makePropertiesArray($properties)
    {
        $pp=array();
        foreach ($properties as $key => $value) {
            # code...
            //dd($value);
            $pp['name']=$key;
            $pp['value']=$value;

            $data[]=$pp;
        }

        return $data;
    }
    public function makeContact($contactArr, $shop)
    {
        //  print_r($contactArr);
        return $this->createCustomer($contactArr, $shop);
    }
    public function createCustomer($contactArr, $shop)
    {
        $shopData= ShopifyApp::shop_get($shop);
        $CustomerSearch=$shopData->api()->request('GET', '/admin/customers/search.json?query='.$contactArr['checkout']['email_or_phone']);
        return $CustomerSearch->body->customers{0}->id;
    }
}
