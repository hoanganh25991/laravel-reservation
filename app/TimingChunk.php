<?php

namespace App;

use App\OutletReservationSetting as Setting;

class TimingChunk extends LightModel{
    protected $visible = [
        'time',
        'session_type',
        'first_arrival_time',
        'interval_minutes',
        'capacity_1',
        'capacity_2',
        'capacity_3_4',
        'capacity_5_6',
        'capacity_7_x',
        'max_pax'
    ];

    public function getMaxPaxAttribute($val){
        if(is_null($val)){
            return Setting::TIMING_MAX_PAX;
        }

        return $val;
    }
}
