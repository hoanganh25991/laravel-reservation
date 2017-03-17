<?php

namespace App\Listeners;

use App\Reservation;
use App\Events\SentSMS;

class SentSMSListener{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(){
        //
    }

    /**
     * Handle the event.
     *
     * @param  SMSSent $event
     * @return void
     */
    public function handle(SentSMS $event){
        $reservation = $event->reservation;
        $reservation->status = Reservation::REMINDER_SENT;
        $reservation->save();
    }
}
