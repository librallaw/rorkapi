<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $guarded = [];

    public function category(){
        $this->belongsTo("App\Category",'name','category_name');
    }

}
