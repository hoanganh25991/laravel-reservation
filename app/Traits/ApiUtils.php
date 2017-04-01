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
}
