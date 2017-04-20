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
        // When customer pick up the reservation
        // Do confirm BEFORE reminder sent
        // Quite crazy right!!
        // Only up to  REMINDER_SENT
        // Not down to REMINDER_SENT
        if($reservation->status < Reservation::REMINDER_SENT){
            $reservation->status = Reservation::REMINDER_SENT;
            $reservation->save();
        }
    }
}
