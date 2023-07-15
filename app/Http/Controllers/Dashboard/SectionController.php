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

class SectionController extends Controller
{
    public function section($id){

        $categories = Category::get();
        $Products = Product::where('category_id',$id )->where('is_active' , 1)->get();

        return view('dashboard.section.section_one',compact('categories' ,'Products','id') ) ;
        
    }
       
    
}//end of controller
