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

class ProductController extends Controller
{
    public function getcheckoutData(Request $request)
    {
        //print_r($request->checkouttoken);
        $shop= ShopifyApp::shop_get('ninja-shipping.myshopify.com');
        $shopdata=$shop->api()->request('GET', '/admin/checkouts/2493e1a0d27052be80929f12a5f0959b.json');
        //dd($shopdata);
        return response()->json(['status'=>'success','code'=>200,'products' => $shopdata,]);
    }


    public function Editproduct(Request $request)
    {
        $productData=$request->id;
        $prddata = DB::table('tbl_products_settings')->where('productId', $request->id)->first();
        $quantity_set = DB::table('tbl_quantity_set')->where('productId', $request->id)->get();
        $production_set = DB::table('tbl_production_set')->where('productId', $request->id)->get();
        $shipping_set = DB::table('tbl_shipping_set')->where('productId', $request->id)->get();
        $size_set = DB::table('tbl_size_set')->where('productId', $request->id)->get();
        return View::make('shopify-app::home.product', ['productData'=>$productData,'quantity_set'=>$quantity_set,'size_set'=>$size_set,'prddata'=>$prddata,'production_set'=>$production_set,'shipping_set'=>$shipping_set]);
    }
    public function updatehw(Request $request)
    {
        $is_duplicate = DB::table('tbl_products_settings')->where('productId', $request->productId)->first();
        if ($is_duplicate) {
            $qtyset=DB::table('tbl_products_settings')->update(['productId'=>$request->productId,'is_custom_hw'=>$request->is_custom_hw]);
            return response()->json([
                  'status' => 'success',
                  'code' => 200
              ]);
        } else {
            $qtyset=DB::table('tbl_products_settings')->insert(['productId'=>$request->productId,'is_custom_hw'=>$request->is_custom_hw]);
            return response()->json([
                  'status' => 'success',
                  'code' => 200
              ]);
        }
    }


    //Cps Update
    public function cps_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
              'minsqft' => 'required|numeric',
              'maxsqft'=>'required|numeric',
              'cps'=>'required|numeric'
          ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $error_msg = $errors->first();
            return response()->json([
                          'status' => 'Fail',
                          'code' => 100,
                          'msg' => $error_msg
                      ]);
        } else {
            $is_duplicate = DB::table('tbl_sqft_details')->where('minsqft', $request->minsqft)->where('maxsqft', $request->maxsqft)->first();
            if ($is_duplicate) {
                return response()->json([
                          'status' => 'Fail',
                          'code' => 100,
                          'msg'=>'Duplicate Record'
                      ]);
                exit;
            }

            $qtyset=DB::table('tbl_sqft_details')->insert(['maxsqft'=>$request->maxsqft,'minsqft'=>$request->minsqft,'cps'=>$request->cps]);
            if ($qtyset) {
                return response()->json([
                          'status' => 'success',
                          'code' => 200
                      ]);
            } else {
                return response()->json([
                          'status' => 'Fail',
                          'code' => 100
                      ]);
            }
        }
    }

    public function setupData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'setupcharge' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                        'status' => 'Fail',
                        'code' => 100
                    ]);
        } else {
            $is_duplicate = DB::table('tbl_products_settings')->where('productId', $request->productId)->where('setup_charge', $request->setupcharge)->first();
            if ($is_duplicate) {
                $qtyset=DB::table('tbl_products_settings')->update(['productId'=>$request->productId,'setup_charge'=>$request->setupcharge]);
                return response()->json([
                        'status' => 'success',
                        'code' => 200
                    ]);
            } else {
                $qtyset=DB::table('tbl_products_settings')->insert(['productId'=>$request->productId,'setup_charge'=>$request->setupcharge]);
                return response()->json([
                        'status' => 'success',
                        'code' => 200
                    ]);
            }
        }
    }



    public function quantity_set(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productId' => 'required',
            'qtyset' => 'required',

        ]);


        if ($validator->fails()) {
            $errors = $validator->errors();
            $error_msg = $errors->first();


            return response()->json([
                        'status' => 'Fail',
                        'code' => 100,
                        'msg'  =>$error_msg
                    ]);
        } else {
            $is_duplicate = DB::table('tbl_quantity_set')->where('productId', $request->productId)->where('quantity_set', $request->qtyset)->first();
            if ($is_duplicate) {
                return response()->json([
                        'status' => 'Fail',
                        'code' => 100,
                        'msg'=>'Duplicate Record'
                    ]);
                exit;
            }

            $qtyset=DB::table('tbl_quantity_set')->insert(['productId'=>$request->productId,'quantity_set'=>$request->qtyset]);
            if ($qtyset) {
                return response()->json([
                        'status' => 'success',
                        'code' => 200
                    ]);
            } else {
                return response()->json([
                        'status' => 'Fail',
                        'code' => 100
                    ]);
            }
        }
    }

    public function production_set_update(Request $request)
    {

        //option_when
        //  $request->all();
        if (isset($request->is_disable)) {
            $validator = Validator::make($request->all(), [
                'productId' => 'required',
                'option_title'=>'required',
                'option_days'=>'required|numeric',
                'option_type' => 'required|numeric',
                'option_value'=>'required|numeric',
                'option_when'=>'required|numeric',
                'greater_value'=>'required|numeric'

            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'productId' => 'required',
                'option_title'=>'required',
                'option_days'=>'required|numeric',
                'option_type' => 'required|numeric',
                'option_value'=>'required|numeric',

            ]);
        }

        if ($validator->fails()) {
            $errors = $validator->errors();
            $error_msg = $errors->first();
            return response()->json([
                          'status' => 'Fail',
                          'code' => 100,
                          'msg' => $error_msg
                      ]);
        } else {
            $is_duplicate = DB::table('tbl_production_set')->where('productId', $request->productId)->where('option_title', $request->option_title)->where('option_days', $request->option_days)->first();
            if ($is_duplicate) {
                return response()->json([
                          'status' => 'Fail',
                          'code' => 100,
                          'msg'=>'Duplicate Record'
                      ]);
                exit;
            }
            if (isset($request->is_disable)) {
                $qtyset=DB::table('tbl_production_set')->insert(['productId'=>$request->productId,'option_days'=>$request->option_days,'option_title'=>$request->option_title,'option_type'=>$request->option_type,'option_value'=>$request->option_value, 	'is_disable'=>$request->is_disable, 'option_when'=>$request->option_when,'greater_value'=>$request->option_when]);
            } else {
                $qtyset=DB::table('tbl_production_set')->insert(['productId'=>$request->productId,'option_days'=>$request->option_days,'option_title'=>$request->option_title,'option_type'=>$request->option_type,'option_value'=>$request->option_value]);
            }
            if ($qtyset) {
                return response()->json([
                          'status' => 'success',
                          'code' => 200
                      ]);
            } else {
                return response()->json([
                          'status' => 'Fail',
                          'code' => 100
                      ]);
            }
        }
    }

    public function shipping_set_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'productId' => 'required',
                'option_title'=>'required',
                'option_days'=>'required|numeric',
                'option_type' => 'required|numeric',
                'option_value'=>'required|numeric',

            ]);


        if ($validator->fails()) {
            $errors = $validator->errors();
            $error_msg = $errors->first();
            return response()->json([
                          'status' => 'Fail',
                          'code' => 100,
                          'msg' => $error_msg
                      ]);
        } else {
            $is_duplicate = DB::table('tbl_shipping_set')->where('productId', $request->productId)->where('option_title', $request->option_title)->where('option_days', $request->option_days)->first();
            if ($is_duplicate) {
                return response()->json([
                          'status' => 'Fail',
                          'code' => 100,
                          'msg'=>'Duplicate Record'
                      ]);
                exit;
            }

            $qtyset=DB::table('tbl_shipping_set')->insert(['productId'=>$request->productId,'option_days'=>$request->option_days,'option_title'=>$request->option_title,'option_type'=>$request->option_type,'option_value'=>$request->option_value]);

            if ($qtyset) {
                return response()->json([
                          'status' => 'success',
                          'code' => 200
                      ]);
            } else {
                return response()->json([
                          'status' => 'Fail',
                          'code' => 100
                      ]);
            }
        }
    }
    public function size_set_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productId' => 'required',
            'height' => 'required|numeric',
            'width'=>'required|numeric',

        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $error_msg = $errors->first();
            return response()->json([
                        'status' => 'Fail',
                        'code' => 100,
                        'msg' => $error_msg
                    ]);
        } else {
            $is_duplicate = DB::table('tbl_size_set')->where('productId', $request->productId)->where('height', $request->height)->where('width', $request->width)->first();
            if ($is_duplicate) {
                return response()->json([
                        'status' => 'Fail',
                        'code' => 100,
                        'msg'=>'Duplicate Record'
                    ]);
                exit;
            }

            $qtyset=DB::table('tbl_size_set')->insert(['productId'=>$request->productId,'height'=>$request->height,'width'=>$request->width]);
            if ($qtyset) {
                return response()->json([
                        'status' => 'success',
                        'code' => 200
                    ]);
            } else {
                return response()->json([
                        'status' => 'Fail',
                        'code' => 100
                    ]);
            }
        }
    }


    public function uploadImageData(Request $request)
    {
        $publiUrl= url('/public/upload');
        $baseurl=str_replace("index.php/", "", $publiUrl);
        $imageUrl_dry='';
        if ($request->hasFile('uploadImg')) {
            $rules = array(
                            'uploadImg' => 'required | mimes:jpeg,jpg,png,JPG,PNG,JPEG',
                        );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => '100', 'msg'=>"Invalid Image!."]);
                exit;
            } else {
                $imageName1 = time().'-'.$request->uploadImg->getClientOriginalName();
                $image1 = $request->file('uploadImg');
                if ($image1->move(public_path('upload'), $imageName1)) {
                    $imageUrl_dry=$baseurl."/".$imageName1;
                    return response()->json(['status' => 'success', 'code' => '200', 'msg'=>"Uploaded", "imageUrl"=>$imageUrl_dry]);
                    exit;
                }
            }
        } else {
            return response()->json(['status' => 'error', 'code' => '100', 'msg'=>"Invalid Image!."]);
            exit;
        }
    }

    public function getProducts_details(Request $request)
    {
        $timezz=  file_get_contents("https://ipapi.co/timezone");


        $time = Carbon::now($timezz)->format('H');
        if ($time < 11) {
            //  echo "Yes";
            $date = Carbon::yesterday($timezz)->format('d-M-Y');
        } else {
            $date = Carbon::today($timezz)->format('d-M-Y');
        }

        //dd($time);



        //dd($date);
        //$dtToronto = Carbon::create(2012, 1, 1, 0, 0, 0, 'America/Toronto');
        //  exit;



        $data = $request->all();
        if ($request['product_id']) {
            $setup_charge=DB::table('tbl_products_settings')->where('productId', $request['product_id'])->first();
            $sqft=DB::table('tbl_sqft_details')->get();
            $table = DB::table('tbl_size_set')->where('productId', $request['product_id'])->get();
            $quantity = DB::table('tbl_quantity_set')->where('productId', $request['product_id'])->get();
            $production = DB::table('tbl_production_set')->where('productId', $request['product_id'])->get();
            $shipping = DB::table('tbl_shipping_set')->where('productId', $request['product_id'])->get();
            return response()->json(['status'=>'success','code'=>200,'time'=>$time,'date'=>$date,'setup_charge'=>$setup_charge,'sqtft'=>$sqft,'size_set' => $table, 'quantity_set'=>$quantity, 'production_set'=>$production, 'shipping_set'=>$shipping]);
        } else {
            return response()->json(['status' => 'error', 'code' => '100', 'msg'=>"Invalid product_id!."]);
            exit;
        }
    }

    public function getprodcts()
    {
        $shop= ShopifyApp::shop_get('dirt-cheap-stickers.myshopify.com');
        $shopdata=$shop->api()->request('GET', '/admin/products.json');
        //dd($shopdata);
        return response()->json(['status'=>'success','code'=>200,'products' => $shopdata,]);
        //return View::make('shopify-app::home.index',['shop'=>$shopdata->body->shop]);
    }

    public function createDraftOrder(Request $request)
    {
        $request->all();
        //dd($request->all());
        $totalPrice=$request['total_price'];
        //dd($request['items']);
        $item=array();
        foreach ($request['items'] as $singleItem) {
            $prdData['title']=$singleItem['title'];
            $prdData['price']=$this->returnProductPrice($singleItem['properties'])/$singleItem['quantity'];
            $prdData['properties']=$this->makePropertiesArray($singleItem['properties']);
            $prdData['quantity']=$singleItem['quantity'];

            $item[]=$prdData;
        }
        $draftt=array('draft_orders'=>array('line_items'=>$item));
        //echo json_encode($draftt);

        $shop= ShopifyApp::shop_get('dirt-cheap-stickers.myshopify.com');
        $shopdata=$shop->api()->request('POST', '/admin/draft_orders.json', ['draft_order'=>array('line_items'=>$item)]);

        return response()->json(['status'=>'success','code'=>200,'orderData' => $shopdata,]);
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

    public function returnProductPrice($properties)
    {
        return $properties['product_price'];
    }
}
