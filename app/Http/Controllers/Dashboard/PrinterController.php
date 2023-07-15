<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Category;
use App\Models\Order;
use App\Models\Printer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class PrinterController extends Controller
{
    
    public function edit($id)
    {
        
        $printer= Printer::findOrFail($id);
        
        $categories = Category::all();
        
      
        return view('dashboard.print.edit', compact('categories', 'printer'));

    

    }//end of edit

   public function update(Request $request,$id)
    {
        
      

        $request_data = $request->all();

        $printer= Printer::findOrFail($id);

        $printer->update($request_data);
        
        $printer->save();
        
        
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.welcome');

    }//end of update







}//end of controller
