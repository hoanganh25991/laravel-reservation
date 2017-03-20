<?php

namespace App\Listeners;

use App\Reservation;
use App\Traits\SendSMS;
use App\Jobs\SendConfirmSMS;
//use App\Events\SentReminderSMS;
use App\Exceptions\SMSException;
use App\Events\ReservationReserved;
use Illuminate\Support\Facades\Log;
use App\OutletReservationSetting as Setting;

class ReservationReservedListener{
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
     * @param  ReservationReserved $event
     * @throws SMSException
     */
    public function handle(ReservationReserved $event){
        /** @var Reservation $reservation */
        $reservation = $event->reservation;

        /**
         * Base on config for reservation
         * Should Send Confirm SMS
         */
        if($reservation->shoudlSendSMSOnBooking()){
            $telephone    = $reservation->full_phone_number;
            $message      = $this->getMessage($reservation);
            $sender_name  = Setting::smsSenderName();
            $success_sent = $this->sendOverHoiio($telephone, $message, $sender_name);

            if($success_sent){
                //event(new SentSMS($reservation));
                //Log::info($reservation->confirm_sms_date);
               Log::info('Success send sms on booking');
            }else{
                throw new SMSException('SMS not sent');
            }
        }
        
        /**
         * Base on config for reservation
         * Should send reminder sms
         * (send confirmation sms)
         */
        if($reservation->shouldSendConfirmSMS()){
            $send_confirm_sms = (new SendConfirmSMS($reservation))->delay($reservation->confirm_sms_date);
            dispatch($send_confirm_sms);
        }

    }

    private function getMessage($reservation){
        //send out an SMS
        $long_datetime_str = $reservation->date->format('on M d Y at H:i');
        return "Your reservation at $reservation->outlet_name $long_datetime_str has been received. Reservation code: $reservation->confirm_id";

    }


}
