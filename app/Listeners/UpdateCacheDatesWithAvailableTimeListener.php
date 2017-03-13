<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\OutletReservationSetting as Setting;
use App\Events\ShouldUpdateCacheDatesWithAvailableTimeEvent;

class UpdateCacheDatesWithAvailableTimeListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ShouldUpdateCacheDatesWithAvailableTimeEvent  $event
     * @return void
     */
    public function handle() {
        $filename = static::getCacheFileName('SHOULD_UPDATE_DATES_WITH_AVAILABLE_TIME');
        Cache::put($filename, true, 24 * 60);
    }
    
    public static function getCacheFileName($key = 'SHOULD_UPDATE_DATES_WITH_AVAILABLE_TIME'){
        $outlet_id = Setting::outletId();
        switch($key){
            case 'SHOULD_UPDATE_DATES_WITH_AVAILABLE_TIME':
                $filename = "SHOULD_UPDATE_DATES_WITH_AVAILABLE_TIME_outlet_$outlet_id";
                break;
            default:
                $filename = 'SHOULD_UPDATE_DATES_WITH_AVAILABLE_TIME';
        }
        
        return $filename;
    }
}
