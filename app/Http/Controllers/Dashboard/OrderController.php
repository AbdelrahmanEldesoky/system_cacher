<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Client;
use App\Models\Category;
use App\Models\Order;
use App\Models\Printer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::get();

        $orders = Order::whereHas('client' , function ($q) use ($request) {
            return $q->where('name', 'like', '%' . $request->search . '%');
        })->where(['is_active' => '1'])->paginate(5);

        $order_done = DB::table('product_order')
        ->select('order_id','is_finsh')->where('is_finsh',0)->distinct()->get();




        return view('dashboard.orders.index', compact('orders','categories','order_done'));

    }//end of index

    public function products(Order $order)
    {

        $categories = Category::get();
        $products = $order->products;

        return view('dashboard.orders._products', compact('order', 'products','categories'));

    }//end of products


    public function printproducts(Order $order)
    {
        $id  = 1;
        $products = $order->products;
        
       $printer= Printer::findOrFail($id);
        
        return view('dashboard.orders.printtest', compact('order', 'products' , 'printer'));

    }


    public function destroy(Order $order)
    {

        foreach ($order->products as $product) {

            $product->update([
                'stock' => $product->stock + $product->pivot->quantity
            ]);

        }//end of for each

        Client::where('id',$order->client_id)->update([
            'is_active'=>0
        ]);
        $order->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.orders.index');

    }//end of order

}//end of controller
