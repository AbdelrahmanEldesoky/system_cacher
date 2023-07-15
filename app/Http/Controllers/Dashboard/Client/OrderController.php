<?php

namespace App\Http\Controllers\Dashboard\Client;

use App\Models\Category;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class OrderController extends Controller
{
    public function create(Client $client)
    {

        $categories = Category::with(['products'=>function($q){
            $q->where('is_active','=',1);
        }])->get();


        $orders = $client->orders()->with('products')->paginate(5);

        return view('dashboard.clients.orders.create', compact( 'client', 'categories', 'orders'));

    }//end of create

    public function store(Request $request, Client $client)
    {
        $request->validate([
            'products' => 'required|array',
        ]);

        $this->attach_order($request, $client);

        $client->update(['is_active'=>'1']);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.orders.index');
    }//end of store

    public function edit(Client $client, Order $order)
    {
        $categories = Category::with(['products'=>function($q){
            $q->where('is_active','=',1);
        }])->get();
        $orders = $client->orders()->with('products')->paginate(5);
        return view('dashboard.clients.orders.edit', compact('client', 'order', 'categories', 'orders'));

    }//end of edit

    public function update(Request $request, Client $client, Order $order)
    {
        //return $request;

        $request->validate([
            'products' => 'required|array',
        ]);

        $this->detach_order($order);
        $this->attach_order($request, $client);

        $max_id = Order::max('id');
        $sum_total = Order::where('is_active',1)->where('client_id',$client->id)->sum('total_price');


        $discount = $sum_total - $request->discount;
        Order::where('is_active',1)->where('client_id',$client->id)
        ->update(['total_price'=> $discount ]);


        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.orders.index');

    }//end of update


    public function add(Client $client, Order $order)
    {


        $categories = Category::with(['products'=>function($q){
            $q->where('is_active','=',1);
        }])->get();
        $orders = $client->orders()->with('products')->paginate(5);

        return view('dashboard.clients.orders.add', compact( 'client', 'categories', 'orders'));
    }//end of create



    public function storeadd(Request $request, Client $client)
    {

        $request->validate([
            'products' => 'required|array',
        ]);

        $this->attach_order($request, $client);


        $max_id = Order::max('id');
        $sum_total = Order::where('is_active',1)->where('client_id',$client->id)->sum('total_price');

        DB::table('product_order as op')
        ->join('orders as o', 'op.order_id', '=', 'o.id')
        ->where('o.is_active', 1)->where('o.client_id',$client->id)
        ->update([ 'op.order_id' => $max_id ]);

            Order::where('is_active',1)->where('client_id',$client->id)
        ->update(['total_price'=>$sum_total]);


        $o=DB::table('orders')
        ->select('id')
        ->whereNotIn('id', DB::table('product_order')->select('order_id'))
        ->delete();


        $client->update(['is_active'=>'1']);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.orders.index');
    }//end of store




    private function attach_order($request, $client)
    {
        $order = $client->orders()->create([]);
        $order->create_order = Carbon::now();
        //$order->update(['discount',$request->discount]);

        $d =Order::where('id',$order->id);

        if($request->discount == null) {
            $d->update([ 'discount' =>0]);
        }else{
            $d->update([ 'discount' =>$request->discount]);
        }



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
