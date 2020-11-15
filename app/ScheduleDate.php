<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleDate extends Model
{
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s.u';
    }

    protected $guarded = [];

//    public function schedules(){
//        return $this->hasMany("App\StationSchedule",'date','schedule_date');
//    }
}
