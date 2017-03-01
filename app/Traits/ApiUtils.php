<?php
namespace App\Traits;

use Carbon\Carbon;
use App\OutletReservationSetting as Setting;

trait ApiUtils{

    protected function getMinutes($time){
        $timeInfo = explode(":", $time);
        $hour = $timeInfo[0];
        $minute = $timeInfo[1];
        
        return $hour * 60 + $minute;
    }

    protected function availableDateRange(){
        $today = Carbon::now(Setting::TIME_ZONE);

        $query_max_day = Setting::where([
            'outlet_id' =>  1,
            'setting_group' => 'BUFFERS',
            'setting_key' => 'MAX_DAYS_IN_ADVANCE'
        ])->first();

        $max_days_in_advance = !is_null($query_max_day) ? $query_max_day : Setting::MAX_DAYS_IN_ADVANCE;


        $max_day = $today->copy()->addDays($max_days_in_advance);
        
        return [$today, $max_day];
    }
}
