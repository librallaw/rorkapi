<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Category;


class Playlist extends Model
{
    protected $guarded = [];

    public function category(){

        return $this->belongsTo('App\Category', 'category_id','unique_id');
    }


    public function owner(){

        return $this->belongsTo('App\User', 'owner_id','unique_id');
    }

}
