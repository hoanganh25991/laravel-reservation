<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Reservation;
use App\Traits\SendSMS;
use Illuminate\Bus\Queueable;
use App\Events\SentReminderSMS;
use App\Exceptions\SMSException;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\OutletReservationSetting as Setting;

class HoiJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use SendSMS;

    /**
     * Create a new job instance.
     *
     */
    public function __construct(){}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $this->sendConfirmSMS();
        
        
        
        /**
         * Self dispatch jobs
         * As internal loop after 5 minutes (1 on DEV)
         */
        $interval_in_minutes = env('APP_ENV') != 'production' ? 1 : 5;
        $delay_time          = Carbon::now(Setting::timezone())->addMinutes($interval_in_minutes);

        $hoi_jobs = (new HoiJobs)->delay($delay_time);
        Log::info('Redispatch as interval loop');
        dispatch($hoi_jobs);
    }

    public function sendConfirmSMS(){
        /**
         * Pop out reservations, which has send_confirmation_by_timestamp
         * Less than current
         */
        $today = Carbon::now(Setting::timezone());
//        $today_str = $today->format('Y-m-d H:i:s');
//        /**
//         * Find reservation RESERVED
//         * Which one send confirmation has passed
//         */
        $reservations =
            Reservation::where([
                ['status', '=', Reservation::RESERVED],
                ['send_sms_confirmation', '=', 1],
//                ['send_confirmation_by_timestamp', '<=', $today_str]
            ])
            ->get();

        $need_send_reminder_reservations =
            $reservations
                ->filter(function(Reservation $reservation) use($today){
                    return $reservation->send_confirmation_by_timestamp->lte($today);
                })->values();
        
        $need_send_reminder_reservations
            ->each(function(Reservation $reservation){
                $telephone   = $reservation->full_phone_number;
                $message     = $reservation->confirmation_sms_message;
                $sender_name = Setting::smsSenderName();

                $success_sent = $this->sendOverNexmo($telephone, $message, $sender_name);

                if($success_sent){
                    Log::info('Success send sms to reminder');
                    event(new SentReminderSMS($reservation));
                }else{
                    throw new SMSException('SMS not sent');
                }
            });
    }
    
}
