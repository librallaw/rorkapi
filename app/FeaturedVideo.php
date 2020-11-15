<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeaturedVideo extends Model
{
    public function video(){
        return $this->belongsTo("App\Video",'video_id','unique_id');
    }
}
