<?php

namespace App;

use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
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

    public function getSearchResult(): SearchResult
    {
        return new SearchResult(
            $this,
            $this->title
        );
    }



    public function station()
    {
        return $this->belongsTo(Station::class,'owner_id','unique_id');
    }
}
