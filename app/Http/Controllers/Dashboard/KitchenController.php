<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Category;
use App\Models\Client;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductTranslation;
use DB;

class KitchenController extends Controller
{
    public function index(Request $request)
    {


        $categories = Category::get();
        $orders = Order::whereHas('client' , function ($q) use ($request) {
            return $q->where('name', 'like', '%' . $request->search . '%');
        })->where(['is_active' => '1'])->paginate(8);


        return view('dashboard.kitchen.index_place',compact('categories','orders'));

    }//end of index

    public function done($client)
    {
        $categories = Category::get();

        $products = ProductTranslation::join('product_order','product_order.product_id','product_translations.product_id')
        ->join('orders','orders.id','product_order.order_id')
        ->select( 'name as product_name', 'features','quantity','note' , 'orders.client_id')
        ->where(['orders.is_active'=>1])
        ->where('client_id',$client)->where('is_finsh',0)->get();

        $clients = ProductTranslation::join('product_order','product_order.product_id','product_translations.product_id')
                          ->join('orders','orders.id','product_order.order_id')
                          ->select( 'product_order.id','name as product_name', 'features','quantity','note', 'orders.client_id')
                          ->where(['orders.is_active'=>1])
                          ->where('client_id',$client)->where('is_finsh',0)->get();

                          return view('dashboard.kitchen.done',compact('categories','clients','products'));

    }//end of index


    public function notdone($client)
    {
        $categories = Category::get();

        // $products = ProductTranslation::join('product_order','product_order.product_id','product_translations.product_id')
        // ->join('orders','orders.id','product_order.order_id')
        // ->select( 'name as product_name', 'features','quantity','note' , 'orders.client_id')
        // ->where(['orders.is_active'=>1])
        // ->where('client_id',$client)->where('is_finsh',0)->get();

        $clients = ProductTranslation::join('product_order','product_order.product_id','product_translations.product_id')
                          ->join('orders','orders.id','product_order.order_id')
                          ->select( 'product_order.id','name as product_name', 'features','quantity','note', 'orders.client_id' , 'is_finsh')
                          ->where(['orders.is_active'=>1])
                          ->where('client_id',$client)->get();

                          return view('dashboard.kitchen.notdone',compact('categories','clients'));

    }//end of index




    public function finsh(Request $request ,$id,$client_id)
    {

        DB::table('product_order')->where('id',$id)
          ->update(['is_finsh' => 1]);


        return redirect()->route('dashboard.kitchen-done', $client_id);
    }



    public function traceupdate(Request $request,$id,$client_id)
    {
        $table= DB::table('product_order')
            ->where('id', $id)
            ->update(['is_finsh' =>2 ]);
        return redirect()->route('dashboard.kitchen-notdone',  $client_id);

    }//end of index




}//end of controller
