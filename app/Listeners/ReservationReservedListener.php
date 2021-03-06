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
            $reservation->sendSMSReservationReserved();
        }
        
        /** Base on send email on booking config, decide send */
        if($reservation->shouldSendEmailOnBooking()){
            $customer_name = "$reservation->salutation $reservation->first_name $reservation->last_name";
            $customer      = (object)['email' => $reservation->email, 'name' => $customer_name];
            
            try{
                Mail::to($customer)->send(new EmailOnBooking($reservation));
            }catch(\Exception $e){
                $msg = "Fail to send email on booking. ";
                $exception_msg = $e->getMessage();
                $msg .= "$exception_msg.";

                Log::info($msg);
            }
        }
    }
}
