<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\BookingController;
//use App\OutletReservationSetting as Setting;
use App\Events\ShouldUpdateCacheDatesWithAvailableTimeEvent as Event;

class UpdateCacheDatesWithAvailableTimeListener {

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param  Event $event
     * @throws \Exception
     */
    public function handle(Event $event) {
        $outlet_id = $event->outlet_id;
        $filename  = BookingController::cacheFileName(BookingController::SHOULD_UPDATE_DATES_WITH_AVAILABLE_TIME, $outlet_id);
        Cache::put($filename, true, 24 * 60);
    }
}
