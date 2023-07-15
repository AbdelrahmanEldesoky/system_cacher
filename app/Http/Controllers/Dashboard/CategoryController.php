<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
class CategoryController extends Controller
{
    public function index(Request $request)
    {
        
        $categories = Category::when($request->search, function ($q) use ($request) {

            return $q->whereTranslationLike('name', '%' . $request->search . '%');

        })->latest()->paginate(5);
        $category = Category::get();
        return view('dashboard.categories.index', compact('categories'));

    }//end of index

    public function create()
    {
        $categories = Category::get();
        $category = Category::get();
        return view('dashboard.categories.create', compact('category','categories'));

    }//end of create

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
 
        ];


        $request->validate($rules);

        $request_data = $request->all();

        if ($request->image) {

            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save('/home/u516457093/domains/scarfaceonline.site/public_html/uploads/product_images/'. $request->image->hashName());

            $request_data['image'] = $request->image->hashName();

        }//end of if

        $product= Category::create($request_data);
        $product->name = $request->name;

        $product->save();


     //   $request->validate($rules);

        //Category::create($request->all());
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.categories.index');

    }//end of store

    public function edit(Category $category)
    { 
        $categories = Category::get();
        return view('dashboard.categories.edit', compact('category','categories'));

    }//end of edit

    public function update(Request $request, Category $category)
    {

        $rules = ['name' => 'required'];

        $request->validate($rules);

        $request_data = $request->all();

        if ($request->image) {

            if ($category->image != 'default.png') {

                Storage::disk('public_uploads')->delete('/product_images/' . $category->image);

            }//end of if

            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save('/home/u516457093/domains/scarfaceonline.site/public_html/uploads/product_images/' . $request->image->hashName());

            $request_data['image'] = $request->image->hashName();

        }//end of if

        $category->update($request_data);
        
          $category->name = $request->name;
         
          
            $category->save();
        
        
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.categories.index');

    }//end of update

    public function destroy(Category $category)
    {
        $category->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.categories.index');

    }//end of destroy

}//end of controller
