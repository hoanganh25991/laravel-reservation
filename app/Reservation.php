<?php

namespace App;

use Carbon\Carbon;
//use Hashids\Hashids;
use App\Traits\ApiUtils;
use App\Events\ReservationCreated;
use Illuminate\Support\Facades\Log;
use App\OutletReservationSetting as Setting;

/**
 * @property mixed reservation_timestamp
 * @see Reservation::getReservationTimestampAttribute
 * 
 * @property Carbon date
 * @see Reservation::getDateAttribute
 * 
 * @property mixed adult_pax
 * @property mixed children_pax
 * @property mixed pax_size
 * @property mixed id
 * @property mixed status
 * 
 * @property mixed $confirm_id
 * @see Reservation::getConfirmIdAttribute
 * 
 * @property mixed $phone_country_code
 * @property mixed $phone
 * 
 * @property mixed $full_phone_number
 * @see Reservation::getFullPhoneNumberAttribute
 * 
 * @property string $outlet_name
 * 
 * @property string $confirm_coming_url
 * @see Reservation::getConfirmCommingUrlAttribute
 * 
 * @property mixed $outlet_id
 * 
 * @property mixed $time
 * @see Reservation::getTimeAttribute
 * 
 * @property mixed $salutation
 * @property mixed $first_name
 * @property mixed $last_name
 * @property mixed $email
 * @property mixed $customer_remarks
 * 
 * @property Carbon $confirm_sms_date
 * @see Reservation::getConfirmSMSDateAttribute
 *
 * @property mixed $send_confirmation_by_timestampe
 * @see Reservation::getSendConfirmationByTimestampAttribute
 */
class Reservation extends HoiModel {

    use ApiUtils;


    /**
     * Reservation status
     */
    const RESERVED        = 100; //init at first
    const REMINDER_SENT   = 200; //sms sent to summary info
    const CONFIRMED       = 300; //remider with CONFIRM link
    const ARRIVED         = 400;
    const USER_CANCELLED  = -100;
    const STAFF_CANCELLED = -200;
    const NO_SHOW         = -300;

    protected $table = 'reservation';

    protected $guarded = ['id'];

    protected $fillable = [
        'outlet_id',
        'customer_id',
        'salutation',
        'first_name',
        'last_name',
        'email',
        'phone_country_code',
        'phone',
        'customer_remarks',
        'adult_pax',
        'children_pax',
        'reservation_timestamp',
        'table_layout_id',
        'table_layout_name',
        'table_name',
        'staff_remarks',
        'status',
        'send_confirmation_by_timestamp',
        'send_sms_confirmation',
        'send_email_confirmation',
        'session_name',
        'reservation_code',
        'staff_read_state',
        'payment_id',
        'payment_timestamp',
        'payment_amount',
        'payment_required',
        'is_outdoor'
    ];

    protected $events = [
        'created' => ReservationCreated::class,
    ];

    protected static function boot() {
        parent::boot();
        
        self::creating(function(Reservation $model){
            //Log::info('Interrupt creating of reservation to modify');
            $model->attributes['send_confirmation_by_timestamp'] = $model->confirm_sms_date;
            $model->attributes['status'] = Reservation::RESERVED;
        });

        static::byOutletId();
    }

    public function scopeValidInDateRange($query){
        $date_range = $this->availableOnDay();

        //consider status > CONFIRMED as booked
        return $query->where([
            ['reservation_timestamp', '>=', $date_range[0]->format('Y-m-d H:i:s')],
            ['reservation_timestamp', '<=', $date_range[1]->format('Y-m-d H:i:s')],
            ['status', '>=', Reservation::CONFIRMED]
        ]);
    }
    
    public static function validGroupByDateTimeCapacity(){
        $self = (new Reservation);
        $valid_reservations   = $self->scopeValidInDateRange($self->query())->get();

        $capacity_counted_reservations =
            $valid_reservations
                ->map(function($reservation){
                    $date  = $reservation->date->format('Y-m-d');
                    $time  = $reservation->date->format('H:i');
                    $capacity   = Timing::getCapacityName($reservation->pax_size);

                    return static::groupNameByDateTimeCapacity($date, $time, $capacity);
                })
                ->groupBy(function($group_name){return $group_name;})
                ->map(function($group){return $group->count();});

        return $capacity_counted_reservations;
    }
    
    public static function groupNameByDateTimeCapacity($date, $time, $capacity){
        return "{$date}_{$time}_{$capacity}";
    }

    /**
     * Alias for reservatin_timestamp as Carbon date obj
     * @return Carbon
     */
    public function getDateAttribute(){
        return $this->reservation_timestamp;
    }

    /**
     * Compute reservation time as H:i
     * @return string
     */
    public function getTimeAttribute(){
        return $this->date->format('H:i');
    }

    /*
     * Compute pax size for summary
     */
    public function getPaxSizeAttribute(){
        return ($this->adult_pax + $this->children_pax);
    }

    /**
     * @param $date_tring
     * @return Carbon
     */
    public function getReservationTimestampAttribute($date_tring){
        return Carbon::createFromFormat('Y-m-d H:i:s', $date_tring, Setting::timezone());
    }

    /**
     * Hash reservaion id to generate confirm id
     * Customer will not know the order
     * @return string
     */
    public function getConfirmIdAttribute(){
        $id         = $this->id;
        $confirm_id = Setting::hash()->encode($id);
        
        return $confirm_id;
    }
    
    public function getFullPhoneNumberAttribute(){
        return "{$this->phone_country_code}{$this->phone}";
    }

    /**
     * Base on notification config: HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM
     * determine when should send
     * @return Carbon
     */
    public function getSendConfirmationByTimestampAttribute(){
        $notification_config = Setting::notificationConfig();
        $hours_before_reservation_timing_send_sms = $notification_config(Setting::HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM);

        if(env('APP_ENV') != 'production'){
            return Carbon::now(Setting::timezone())->addMinutes(1);
        }

        return $this->date->subHours($hours_before_reservation_timing_send_sms);
    }

    /**
     * Base on notification config: SEND_SMS_TO_CONFIRM_RESERVATION
     * check should send confirm cms
     * @return bool
     */
    public function shouldSendConfirmSMS(){
        $notification_config = Setting::notificationConfig();
        $should_send_sms_to_confirm_reservation = $notification_config(Setting::SEND_SMS_TO_CONFIRM_RESERVATION) == Setting::SHOULD_SEND;

        return $should_send_sms_to_confirm_reservation;
    }

    public function getConfirmSMSDateAttribute(){
        return $this->send_confirmation_by_timestamp;
    }

    /**
     * Base on notification config: SEND_SMS_ON_BOOKING
     * check should send summary cms on booking
     * @return bool
     */
    public function shoudlSendSMSOnBooking(){
        $notification_config = Setting::notificationConfig();
        $should_send_sms_on_booking = $notification_config(Setting::SEND_SMS_ON_BOOKING) == Setting::SHOULD_SEND;

        return $should_send_sms_on_booking;
    }

    /**
     * Confirm url for reservation
     * @return string
     */
    public function getConfirmComingUrlAttribute(){
        $confirm_id = $this->confirm_id;

        return route("reservation_confirm", compact('confirm_id'));;
    }
}
