<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StationSchedule extends Model
{
    protected $guarded = [];

    public function getDateFormat()
    {
        return 'Y-m-d H:i:s.u';
    }
}
