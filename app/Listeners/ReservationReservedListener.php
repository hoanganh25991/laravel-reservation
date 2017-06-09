<?php

namespace App\Listeners;

use App\Reservation;
use App\Traits\SendSMS;
use App\Mail\EmailOnBooking;
use App\Exceptions\SMSException;
use App\Events\ReservationReserved;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
        
        /** Base on send email on booking config, decide send */
        if($reservation->shouldSendEmailOnBooking()){
            $customer_name = "$reservation->salutation $reservation->first_name $reservation->last_name";
            $customer      = (object)['email' => $reservation->email, 'name' => $customer_name];
            
            Mail::to($customer)->send(new EmailOnBooking($reservation));
        }
    }
}
