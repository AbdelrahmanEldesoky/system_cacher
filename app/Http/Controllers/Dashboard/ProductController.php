<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        $products = Product::when($request->search, function ($q) use ($request) {

            return $q->whereTranslationLike('name', '%' . $request->search . '%');

        })->when($request->category_id, function ($q) use ($request) {

            return $q->where('category_id', $request->category_id);

        })->latest()->paginate(5);

        return view('dashboard.products.index', compact('categories', 'products'));

    }//end of index

    public function create()
    {
        $categories = Category::all();



        return view('dashboard.products.create', compact('categories'));

    }//end of create

    public function store(Request $request)
    {


        $rules = [
            'category_id' => 'required',
            'sale_price' => 'required',
            'name' => 'required',

        ];


        $request->validate($rules);

        $request_data = $request->all();

        if ($request->image) {

            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save('/home/u516457093/domains/scarfaceonline.site/public_html/uploads/product_images/' . $request->image->hashName());

            $request_data['image'] = $request->image->hashName();

        }//end of if

        $product= Product::create($request_data);
        $product->name = $request->name;
        $product->description  = $request->description;
        $product->save();

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.products.index');

    }//end of store

    public function f($id)
    {
        $categories = Category::all();
        $product = Product::findOrFail($id);



        return view('dashboard.products.feature',compact('product','categories'));

    }//end of edit


    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('dashboard.products.edit', compact('categories', 'product'));

    }//end of edit

   public function update(Request $request, Product $product)
    {
        $rules = [
            'category_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'sale_price' => 'required',
            
        ];



        $request->validate($rules);

        $request_data = $request->all();
        
        
            if (!$request->has('is_active')){
                $request->request->add(['is_active' => 0]);
                $request_data['is_active'] = 0;
            }else{
                $request->request->add(['is_active' => 1]);
                $request_data['is_active'] = 1;}



        if ($request->image) {

            if ($product->image != 'default.png') {

                Storage::disk('public_uploads')->delete('/product_images/' . $product->image);

            }//end of if

            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save('/home/u516457093/domains/scarfaceonline.site/public_html/uploads/product_images/' . $request->image->hashName());

            $request_data['image'] = $request->image->hashName();

        }//end of if

        $product->update($request_data);
        
          $product->name = $request->name;
          $product->description = $request->description;
              
            $product->save();
        
        
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.products.index');

    }//end of update


    public function destroy(Product $product)
    {
        if ($product->image != 'default.png') {

            Storage::disk('public_uploads')->delete('/product_images/' . $product->image);

        }//end of if

        $product->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.products.index');

    }//end of destroy

    public function add(Request $request, Product $product)
    {
        $categories = Category::all();
        Feature::create([
            'feature'=>$request->feature,
            'product_id'=>$product->id
        ]);
        return redirect()->route('dashboard.products.index');
    }


    public function indexf(Request $request, Product $product)
    {
        $categories = Category::all();
       $features = Feature::where('product_id',$product->id)->get();

        return view('dashboard.products.featureindex' , compact('product','features','categories'));
    }


    public function c(Request $request, Product $product)
    {
        $categories = Category::all();
       $features = Feature::where('product_id',$product->id)->get();

        return view('dashboard.products.featurec' , compact('product','features','categories'));
    }



    public function editf(Request $request,$id )
    {
        $categories = Category::all();

       $features = Feature::findOrFail($id);
        return view('dashboard.products.featureedit',compact('features','categories'));
    }


    public function updatef(Request $request, $id)
    {
        $categories = Category::all();
        $features = Feature::findOrFail($id);

        $features->update([
            'feature'=>$request->feature
        ]);

        return redirect()->route('dashboard.products.index');
    }

    public function productdestroy($id)
    {
        $features = Feature::findOrFail($id);
        $features->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.products.index');

    }//end of destroy

}//end of controller
