<?php

namespace App\Jobs;

use App\Reservation;
use App\Events\SentReminderSMS;
use Illuminate\Bus\Queueable;
use App\Exceptions\SMSException;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\OutletReservationSetting as Setting;

class SendConfirmSMS implements ShouldQueue{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reservation;

    /**
     * Create a new job instance.
     *
     * @param Reservation $reservation
     */
    public function __construct(Reservation $reservation){

    }

    /**
     * Execute the job.
     *
     * @throws SMSException
     */
    public function handle(){
        $reservation = $this->reservation;

        $telephone    = $reservation->full_phone_number;
        $message      = $this->getMessage($reservation);
        $sender_name  = Setting::smsSenderName();

        $success_sent = $this->sendOverHoiio($telephone, $message, $sender_name);
        if($success_sent){
            event(new SentReminderSMS($reservation));
        }else{
            throw new SMSException('SMS not sent');
        }

    }

    private function getMessage(Reservation $reservation){
        $hours_before = $reservation->confirm_SMS_date->diffInHours(Carbon::now(Setting::timezone()));
        $sender_name  = Setting::smsSenderName();
        $time_str     = $reservation->date->format('at H:i');

        $msg  = "You are $hours_before hours from your $sender_name reservation! ";
        $msg .= "$reservation->adult_pax adults $reservation->children_pax children $time_str at $reservation->outlet_name. ";
        $msg .= "Confirm you are coming: $reservation->confirm_comming_url";
        
        return $msg;
    }
}
