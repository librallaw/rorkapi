<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //

    public function category()
    {
        return $this->belongsTo(Product_category::class,"category_id","unique_id");
    }
}
