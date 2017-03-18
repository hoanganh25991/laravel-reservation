<?php
namespace App\Traits;

use Carbon\Carbon;
use App\OutletReservationSetting as Setting;
use Hamcrest\Core\Set;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait ApiUtils{

    protected function getMinutes($time){
        $timeInfo = explode(":", $time);
        $hour = $timeInfo[0];
        $minute = $timeInfo[1];
        
        return $hour * 60 + $minute;
    }

    protected function availableDateRange(){
        $buffer_config = Setting::bufferConfig();
        $max_days_in_advance = $buffer_config('MAX_DAYS_IN_ADVANCE');
        
        $today   = Carbon::now(Setting::timezone());
        $max_day = $today->copy()->addDays($max_days_in_advance);
        
        return [$today, $max_day];
    }
}
