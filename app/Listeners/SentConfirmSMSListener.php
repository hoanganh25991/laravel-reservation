<?php

namespace App\Listeners;

use App\Reservation;
use App\Events\SentReminderSMS;

class SentConfirmSMSListener{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(){}

    /**
     * Handle the event.
     *
     * @param SentReminderSMS|SMSSent $event
     */
    public function handle(SentReminderSMS $event){
        $reservation = $event->reservation;
        $reservation->status = Reservation::REMINDER_SENT;
        $reservation->save();
    }
}
