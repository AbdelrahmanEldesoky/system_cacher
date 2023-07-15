<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $table = 'features';
    protected $guarded = [];
    public function product_f()
    {
        return $this->belongsTo(Feature::class);
    }

}//end of model
