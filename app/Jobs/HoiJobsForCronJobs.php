<?php
namespace App\Jobs;

use App\Outlet;
use Carbon\Carbon;
use App\Reservation;
use App\Traits\SendSMS;
use App\Events\SentReminderSMS;
use App\Exceptions\SMSException;
use Illuminate\Support\Facades\Log;
use App\OutletReservationSetting as Setting;

class HoiJobsForCronJobs {

    use SendSMS;

    public function __construct(){
        $this->sendConfirmSMS();
    }

    public function sendConfirmSMS(){
        /**
         * Pop out reservations, which has send_confirmation_by_timestamp
         * Less than current
         */
        $today   = Carbon::now(Setting::timezone());
        $outlets = Outlet::all();

        $outlets->each(function(Outlet $outlet) use($today){
            /**
             * Explicit tell global query scope
             * Which outlet we are
             */
            session(['outlet_id' => $outlet->id]);
            $notification_config = Setting::notificationConfig();
            $hours_before_reservation_timing_send_sms = $notification_config(Setting::HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM);
            /**
             * Compute reservation timestamp should sent reminder
             */
            $reservation_timestamp     = $today->copy()->subHours($hours_before_reservation_timing_send_sms)->subMinutes(5);
            $reservation_timestamp_str = $reservation_timestamp->format('Y-m-d H:i:s');

            $need_send_reminder_reservations =
                Reservation::where([
                    ['status', Reservation::RESERVED],
                    ['send_sms_confirmation', Setting::SHOULD_SEND],
                    ['reservation_timestamp', '<=', $reservation_timestamp_str]
                ])
                    ->get();

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

        });
    }
}