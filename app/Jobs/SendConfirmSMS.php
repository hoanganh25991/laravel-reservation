<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Reservation;
use App\Traits\SendSMS;
use App\Events\SentReminderSMS;
use Illuminate\Bus\Queueable;
use App\Exceptions\SMSException;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\OutletReservationSetting as Setting;

//use Illuminate\Support\Facades\Log;

class SendConfirmSMS implements ShouldQueue{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use SendSMS;

    /** @var  Reservation $reservation */
    protected $reservation;

    /**
     * Create a new job instance.
     *
     * @param Reservation $reservation
     */
    public function __construct(Reservation $reservation){
        $this->reservation = $reservation;
    }

    /**
     * Execute the job.
     *
     * @throws SMSException
     */
    public function handle(){
        //Log::info('SendConfirmSMS handled');
        $reservation = $this->reservation;
        $telephone    = $reservation->full_phone_number;
        $message      = $this->getMessage($reservation);
        $sender_name  = Setting::smsSenderName();

        $success_sent = $this->sendOverHoiio($telephone, $message, $sender_name);
        if($success_sent){
            Log::info('Success send sms to reminder');
            event(new SentReminderSMS($reservation));
        }else{
            throw new SMSException('SMS not sent');
        }

    }

    private function getMessage(Reservation $reservation){
        $hours_before = $reservation->confirm_sms_date->diffInHours(Carbon::now(Setting::timezone()));
        $sender_name  = Setting::smsSenderName();
        $time_str     = $reservation->date->format('H:i');
        
        $msg  = "You are $hours_before hours from your $sender_name reservation! ";
        $msg .= "$reservation->adult_pax adults $reservation->children_pax children at $time_str at $reservation->outlet_name. ";
        $msg .= "Confirm you are coming: $reservation->confirm_coming_url";
        
        return $msg;
    }
}