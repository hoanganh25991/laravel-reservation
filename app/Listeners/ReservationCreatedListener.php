<?php

namespace App\Listeners;

use App\Jobs\SendConfirmSMS;
use App\Reservation;
use App\Traits\SendSMS;
use App\Events\SentSMS;
use App\Exceptions\SMSException;
use App\Events\ReservationCreated;
use App\OutletReservationSetting as Setting;

class ReservationCreatedListener{
    use SendSMS;

    /**
     * Create the event listener.
     *
     */
    public function __construct(){
        //
    }

    /**
     * Handle the event.
     *
     * @param  ReservationCreated $event
     * @throws SMSException
     */
    public function handle(ReservationCreated $event){
        /** @var Reservation $reservation */
        $reservation = $event->reservation;

        $telephone    = $reservation->full_phone_number;
        $message      = $this->getMessage($reservation);
        $sender_name  = Setting::smsSenderName();
        $success_sent = $this->sendOverHoiio($telephone, $message, $sender_name);

        if($success_sent){
            event(new SentSMS($reservation));
            
            $send_confirm_sms = (new SendConfirmSMS($reservation))->delay(Setting::);
            dispatch($send_confirm_sms);
        }else{
            throw new SMSException('SMS not sent');
        }
    }

    private function getMessage($reservation){
        //send out an SMS
        $long_datetime_str = $reservation->date->format('M d Y');
        return "Your reservation at $reservation->outlet_name on $long_datetime_str has been received. Reservation code: $reservation->confirm_id";

    }


}
