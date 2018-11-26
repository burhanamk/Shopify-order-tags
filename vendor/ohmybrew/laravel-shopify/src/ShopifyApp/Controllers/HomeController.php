<?php

namespace OhMyBrew\ShopifyApp\Controllers;

use Illuminate\Routing\Controller;
//use OhMyBrew\ShopifyApp\Traits\HomeControllerTrait;
use OhMyBrew\ShopifyApp\Facades\ShopifyApp;
use OhMyBrew\ShopifyApp\Models\Shop;
use View;
use Illuminate\Http\Request;
use DB;

class HomeController extends Controller
{
    public function inventory(Request $request)
    {
        $shopifyProducts='';
        //$shop= ShopifyApp::shop();
        //$shopdata=$shop->api()->request('GET', '/admin/shop.json');
        //return View::make('shopify-app::home.inventory', ['shop'=>$request->shop]);
        $limit=10;
        if (!isset($request->page)) {
            $page=1;
        } else {
            $page=$request->page;
        }
        $previous_page='';
        $next_page='';
        $current_page=$page;
        $search='';
        $start=$page*$limit-$limit;
        $tableData['table']="tbl_previous_order";
        $tableData['fields']=['orderid','productid','variantid','quantity_to_fulfill','order_date'];
        $tableData['fields_select']=['orderid','productid','variantid','order_date'];
        $tableData['group']="variantid";
        //$tableData['table']=
        $searchData=  $this->getOnorderProductsQuantity($search, $start, $limit, $tableData);
        //echo '<pre>';
        //print_r($searchData);
        if ($page > 1) {
            $previous_page=$page-1;
        }
        if ($page*$limit < $searchData['totalRow']) {
            $next_page=$page+1;
        }
        $searchData['previous_page']=$previous_page;
        $searchData['current_page']=$current_page;
        $searchData['next_page']=$next_page;
        $searchData['search']=$search;
        if ($searchData['totalRow']>0) {
            if ($searchData['data']) {
                foreach ($searchData['data'] as $Product) {
                    $products[]=$Product->productid;
                }
                $UniqueProducts=array_unique($products);
                //$UniqueProducts
                //  echo '<pre>';
                //  print_r($UniqueProducts);
                $productString=implode(",", $UniqueProducts);
                $shopifyProducts=$this->getProducTData($request->shop, $productString);
            }
        }
        //  echo '<pre>';
        //print_r($shopifyProducts);
        //exit;
        return View::make('shopify-app::home.inventory', ['shop'=>$request->shop,'searchData'=>$searchData, 'shopifyProducts'=>$shopifyProducts]);
    }

    public function getProducTData($shop_domain, $productString)
    {
        $shopData=$this->getShopDetails($shop_domain);
        $shop= ShopifyApp::shop_get($shopData->shopify_domain);
        $products=$shop->api()->request('GET', '/admin/products.json?ids='.$productString);
        return $products->body->products;
    }
    public function getOnorderProductsQuantity($search, $start, $limit, $tableData)
    {
        $query=DB::table($tableData['table'])->select($tableData['fields_select'])->selectRaw('sum(quantity_to_fulfill) as quantity_to_fulfill');

        if ($search) {
            $i=1;
            foreach ($tableData['fields'] as $tableField) {
                if ($i==1) {
                    $query->where($tableField, 'like', "%{$search}%");
                } else {
                    $query->orWhere($tableField, 'like', "%{$search}%");
                }
                $i++;
            }
        }
        if ($tableData['group']!="") {
            $query->groupBy("variantid");
        }


        $query1=$query;
        //print_r($data);
        $count = $query->get()->count();
        $query1->offset($start)->limit($limit);
        $data=$query1->get();
        //  print_r($wordlist);
        //    print_r(DB::getQueryLog());
        return ['data'=>$data,'totalRow'=>$count];
    }



    public function index()
    {
        $shop= ShopifyApp::shop();
        $shopdata=$shop->api()->request('GET', '/admin/shop.json');

        //$shopData=$this->getShopDetails($request->shop);
        //
        // $shop= ShopifyApp::shop_get($shopData->shopify_domain);

        // $scripttag=array("event"=>  env('SHOPIFY_SCRIPTTAG_1_EVENT'),
        // "display_scope"=>  env('SHOPIFY_SCRIPTTAG_1_DISPLAY_SCOPE'),
        // "src"=>env('SHOPIFY_SCRIPTTAG_1_SRC'));
        // $script=$shop->api()->request('POST', '/admin/script_tags.json', ['script_tag' => $scripttag]);
        // print_r($script);

        // $script=$shop->api()->request('GET', '/admin/script_tags.json');
        // //dd($script);
        // $tagEnable=0;
        // $tagId='';
        // if (isset($script->body->script_tags)) {
        //     foreach ($script->body->script_tags as $tag) {
        //         if (strpos($tag->src, 'checkout_new.js') !== false) {
        //             $tagEnable=1;
        //             $tagId=$tag->id;
        //         }
        //     }
        // }
        // $scripts=$shop->api()->request('DELETE', '/admin/script_tags/'.$tagId.'.json');
        //
        // if ($scripts) {
        //     return response()->json([
        //             'status' => 'success',
        //             'code' => 200,
        //             'msd'=>'Offer Disabled'
        //
        //         ]);
        // }



        return View::make('shopify-app::home.index', ['shop'=>$shopdata->body->shop->myshopify_domain]);
    }
    public function getShopDetails($shopp)
    {
        return  DB::table('shops')->where('shopify_domain', $shopp)->first();
    }

    public function settings()
    {
        $shop= ShopifyApp::shop();
        $shopdata=$shop->api()->request('GET', '/admin/shop.json');

        return View::make('shopify-app::home.settings', ['shop'=>$shopdata->body->shop]);
    }

    public function NinjaOrdersALL()
    {
        $shop= ShopifyApp::shop();
        $shopdata=$shop->api()->request('GET', '/admin/shop.json');

        return View::make('shopify-app::home.orders', ['shop'=>$shopdata->body->shop]);
    }
}
