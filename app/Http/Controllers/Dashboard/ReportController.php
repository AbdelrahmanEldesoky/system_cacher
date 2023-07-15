<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use DB;
class ReportController extends Controller
{
    public function index(Request $request)
    {
        $count_order = Order::when($request->date1, function ($q) use ($request) {
            return $q->whereBetween('create_order', [$request->date1,$request->date2 ]);
        })->count('id');


        $report_a = Order::when($request->date1, function ($q) use ($request) {
            return $q->whereBetween('create_order', [$request->date1,$request->date2 ]);
        })->where(['is_active'=>'1'])->sum('total_price');

        $report_d = Order::when($request->date2, function ($q) use ($request) {

            return $q->whereBetween('create_order', [$request->date1,$request->date2 ]);

        })->where(['is_active'=>'0'])->sum('total_price');

        $a =Carbon::now()->format('Y/m/d');
        $report_aa = Order::where(['create_order'=>$a])->where(['is_active'=>'1'])->sum('total_price');
        $report_dd = Order::where(['create_order'=>$a])->where(['is_active'=>'0'])->sum('total_price');
        $count_order_a = Order::where(['create_order'=>$a])->count('id');
        $orders = Order::where(['create_order'=>$a])->with(['client'])->paginate(5);
        $categories = Category::get();
        return view('dashboard.report.index', compact('orders','report_a','report_d','report_aa' , 'report_dd' ,'count_order_a','count_order' , 'categories'));

    }//end of index

    public function order(Request $request,$client_id)
    {
        $a=Carbon::now()->format('Y/m/d');
        $categories = Category::get();

        $worker_get = Order::join('product_order', 'product_order.order_id', '=', 'orders.id')
        ->join('product_translations', 'product_translations.product_id', '=', 'product_order.product_id')
        ->join('products','products.id', 'product_translations.product_id')
        ->join('clients','clients.id','orders.client_id')
        ->select('orders.id as order_id' , 'product_translations.name as product_name' ,'product_order.quantity as quantity'
                ,'clients.floor as floor' ,'sale_price')
        ->where('orders.client_id',$client_id)
        ->where(['create_order'=>$a])
        ->get();


        return view('dashboard.report.details', compact('worker_get','categories'));

    }//end of create


    public function products ($date1,$date2)
    {
        $categories = Category::get();

        $products = Order::join('product_order','product_order.order_id','orders.id')
                            ->join('product_translations','product_translations.product_id','product_order.product_id')
                            ->select(DB::raw('count(product_order.quantity )as quantity, product_order.product_id, product_translations.name'))
                            ->whereBetween('orders.create_order', [$date1,$date2 ])
                             ->groupBy('product_translations.name','product_order.product_id')->orderBy('quantity', 'DESC')->get();


        return view('dashboard.report.create', compact('products','categories'));

    }//end of create




}//end of controller
