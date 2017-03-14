<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Cache;
//use App\OutletReservationSetting as Setting;
use App\Events\ShouldUpdateCacheDatesWithAvailableTimeEvent as Event;

class UpdateCacheDatesWithAvailableTimeListener {

//    protected $outlet_id;

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
     * @param  Event $event
     * @throws \Exception
     */
    public function handle(Event $event) {
        try{$outlet_id = $event->outlet_id;}catch(\Exception $e){throw $e;}
        $filename = static::getCacheFileName('SHOULD_UPDATE_DATES_WITH_AVAILABLE_TIME', $outlet_id);
        Cache::put($filename, true, 24 * 60);
    }
    
    public static function getCacheFileName($key = 'SHOULD_UPDATE_DATES_WITH_AVAILABLE_TIME', $outlet_id = 1){
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
