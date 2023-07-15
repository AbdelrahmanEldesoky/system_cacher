<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Translatable;
    protected $with = ['translations'];
    protected $guarded = ['id','name','description'];

    public $translatedAttributes = ['name', 'description'];
    protected $appends = ['image_path'];

    public function getActive()
    {
        return $this->is_active == 1 ? 'مفعل' : 'غيرمفعل';
    }
    public function getImagePathAttribute()
    {
        return asset('uploads/product_images/' . $this->image);

    }//end of image path attribute
/*
    public function getProfitPercentAttribute()
    {
        $profit = $this->sale_price - $this->purchase_price;
        $profit_percent = $profit * 100 / $this->purchase_price;
        return number_format($profit_percent, 2);

    }//end of get profit attribute
*/


    public function features()
    {
        return $this->hasMany(Feature::class);
    }//end of products

    public function category()
    {
        return $this->belongsTo(Category::class);

    }//end fo category

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'product_order');

    }//end of orders

}//end of model
