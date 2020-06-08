<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $guarded = [];

    public function category(){
        return $this->belongsTo("App\Category",'category_id','unique_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class,'owner_id','unique_id');
    }
}
