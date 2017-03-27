<?php

namespace App\Listeners;

use App\Reservation;
use App\Traits\SendSMS;
use App\Events\SentReminderSMS;
//use App\Jobs\SendConfirmSMS;
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
        if($reservation->shouldSendSMSOnReserved()){
            $telephone    = $reservation->full_phone_number;
            $message      = $reservation->sms_message_on_reserved;
            $sender_name  = Setting::smsSenderName();
            $success_sent = $this->sendOverNexmo($telephone, $message, $sender_name);

            if($success_sent){
                Log::info('Success send sms on reserved');
            }else{
                throw new SMSException('SMS not sent');
            }
        }
        
        /**
         * Base on config for reservation
         * Should send reminder sms
         * (send confirmation sms)
         */
//        if($reservation->shouldSendConfirmSMS()){
//            $send_confirm_sms = (new SendConfirmSMS($reservation))->delay($reservation->confirm_sms_date);
//            dispatch($send_confirm_sms);
//        }
        /**
         * Replace with interval jobs
         * Which pop out reservations to send
         */

    }
}
