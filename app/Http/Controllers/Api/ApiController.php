<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use function foo\func;
use DB;
class ApiController extends Controller
{

    public function traceorder()
    {
        $table = Order::with('client')->where('is_active',1)->get();


        return response()->json([
            'success'   => true,
            'msg'=>'',
            'data'=> $table
        ]);
    }//end of index


    public function traceid($id)
    {
        $table = Order::with('products')->where('is_active',1)->where('id',$id)->get();

        return response()->json([
            'success'   => true,
            'msg'=>'',
            'data'=> $table
        ]);
    }//end of index
    public function traceupdate(Request $request,$id)
    {
        $table= DB::table('product_order')
            ->where('id', $id)
            ->update(['is_finsh' =>$request->is_finsh ]);

        return response()->json([
            'success'   => true,
            'msg'=>'تم بجاح',
            'data'=> $table
        ]);
    }//end of index




    ######################### table  #################################
       public function table()
    {
        $table = Client::where('type',2)->get();

        return response()->json([
            'success'   => true,
            'msg'=>'',
            'data'=> $table
        ]);
    }//end of index

############# Category  ############
    public function category()
    {
        $categories = Category::with(['products'=>function($q){
            $q->where('is_active','=',1)->with('features');
        }])->get();

        return response()->json([
            'success'   => true,
            'msg'=>'',
            'data'=> $categories
        ]);
    }//end of index

    ################# Product ##################
    public function product($id)
    {

        $product = Product::where('is_active','=',1)->where('category_id',$id)->with('features')->get();

        return response()->json([
            'success'   => true,
            'msg'=>'',
            'data'=> $product
        ]);
    }//end of index

################### Order #########################

    public function store(Request $request, Client $client)
    {

        $request->validate([
            'products' => 'required|array',
        ]);

        $this->attach_order($request, $client);
        $client->update(['is_active'=>'1']);

        $max_id = Order::max('id');
        $sum_total = Order::where('is_active',1)->where('client_id',$client->id)->sum('total_price');

        DB::table('product_order as op')
        ->join('orders as o', 'op.order_id', '=', 'o.id')
        ->where('o.is_active', 1)->where('o.client_id',$client->id)
        ->update([ 'op.order_id' => $max_id ]);

            Order::where('is_active',1)->where('client_id',$client->id)
        ->update(['total_price'=>$sum_total]);




     $o=  DB::table('orders')
       ->select('id')
       ->whereNotIn('id', DB::table('product_order')->select('order_id'))
       ->delete();

        return response()->json([
            'success'   => true,
            'msg'=>'تم الاضافة بنجاح',
            'data'=> 'done'
        ]);

    }//end of store

    public function edit( int $client)
    {
        $worker_get = Order::join('product_order', 'product_order.order_id', '=', 'orders.id')
                           ->join('product_translations', 'product_translations.product_id', '=', 'product_order.product_id')
                           ->join('products','products.id', 'product_translations.product_id')
                           ->select('orders.id as order_id' , 'product_translations.name as product_name' ,'product_order.quantity as quantity'
                                   ,'orders.client_id as client_id' ,'sale_price')
                           ->where('orders.client_id',$client)
                          ->where('orders.is_active',1)
                           ->get();
         return response()->json([
                            'success'   => true,
                            'msg'=>'تم الاضافة بنجاح',
                            'data'=> $worker_get
                        ]);
    }

    public function update(Request $request, Client $client, Order $order)
    {
        $request->validate([
            'products' => 'required|array',
        ]);

        $this->detach_order($order);
        $this->attach_order($request, $client);

        return response()->json([
            'success'   => true,
            'msg'=>'تم الاضافة بنجاح',
            'data'=> 'done'
        ]);
    }//end of update


private function attach_order($request, $client)
    {
        $order = $client->orders()->create([]);
        $order->create_order = Carbon::now();


        $order->products()->attach($request->products);

        $total_price = 0;

        foreach ($request->products as $id => $quantity) {

            $product = Product::FindOrFail($id);
            $total_price += $product->sale_price * $quantity['quantity'];

            $product->update([
                'stock' => $product->stock - $quantity['quantity']
            ]);

        }//end of foreach

        $order->update([
            'total_price' => $total_price
        ]);

    }//end of attach order

    private function detach_order($order)
    {
        foreach ($order->products as $product) {

            $product->update([
                'stock' => $product->stock + $product->pivot->quantity
            ]);

        }//end of for each

        $order->delete();

    }//end of detach order





}//end of controller
