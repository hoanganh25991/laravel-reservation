<?php

namespace App;

use Carbon\Carbon;
//use Hashids\Hashids;
use App\Traits\ApiUtils;
use App\Events\ReservationReserved;
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
 * @property mixed $send_confirmation_by_timestamp
 * @see Reservation::getSendConfirmationByTimestampAttribute
 * 
 * @property mixed $deposit
 * @see Reservation::getDepositAttribute
 *
 * @property mixed send_sms_confirmation
 * @see Reservation::getSendSMSConfirmationAttribute
 *
 * Loading through relationship
 * @property mixed $outlet
 * @see Reservation::outlet
 * 
 * @property mixed $sms_message_on_reserved
 * @see Reservation::getSMSMessageOnReservedAttribute
 * 
 * @property mixed $confirmation_sms_message
 * @see Reservation::getConfirmationSMSMessageAttribute
 */
class Reservation extends HoiModel {

    use ApiUtils;


    /**
     * Reservation status
     */
    const REQUIRED_DEPOSIT= 50;
    const RESERVED        = 100; //init at first
    const REMINDER_SENT   = 200; //sms sent to summary info
    const CONFIRMED       = 300; //remider with CONFIRM link
    const ARRIVED         = 400;
    const USER_CANCELLED  = -100;
    const STAFF_CANCELLED = -200;
    const NO_SHOW         = -300;

    protected $table = 'reservation';

    protected $guarded = ['id'];

    protected $dates = [
        'reservation_timestamp',
        'payment_timestamp',
    ];

    /**
     * Bring these computed field when serialize to JSON
     * @var array
     */
    protected $appends = [
        'full_phone_number',
        'confirm_id'
    ];

    protected $casts = [];

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
        //'created' => ReservationReserved::class,
    ];

    protected static function boot() {
        parent::boot();
        
        self::creating(function(Reservation $reservation){
            /**
             * Auto compute send_confirmation_by_timestamp
             * Base on current config
             */
            $reservation->send_confirmation_by_timestamp = $reservation->send_confirmation_by_timestamp;

            /**
             * Auto compuate send_sms_confirmation
             * Base on current config
             */
            $reservation->send_sms_confirmation = $reservation->send_sms_confirmation;
            
            /**
             * Default with no status explicit bind
             * Reservation consider as RESERVERD
             */
            if(!isset($reservation->attributes['status'])){
                $status = Reservation::RESERVED;
                
                if($reservation->requiredDeposit()){
                    $status = Reservation::REQUIRED_DEPOSIT;
                }
                
                $reservation->status = $status;
            }
        });

        self::created(function(Reservation $reservation){
            if($reservation->status == Reservation::RESERVED){
                event(new ReservationReserved($reservation));
            }
        });

        self::updated(function(Reservation $reservation){
            if($reservation->status == Reservation::RESERVED){
                event(new ReservationReserved($reservation));
            }
        });

        static::byOutletId();
    }

    public function scopeValidInDateRange($query){
        $date_range = $this->availableOnDay();

        //consider status > CONFIRMED as booked
        return $query->where([
            ['reservation_timestamp', '>=', $date_range[0]->format('Y-m-d H:i:s')],
            ['reservation_timestamp', '<=', $date_range[1]->format('Y-m-d H:i:s')],
            ['status', '>=', Reservation::RESERVED]
        ]);
    }
    
    public static function reservedGroupByDateTimeCapacity(){
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
     * Get out reservation timestamp as Carbon Obj
     * Make easier to handle datetime
     * @param $date_tring
     * @return Carbon
     */
//    public function getReservationTimestampAttribute($date_tring){
//        return Carbon::createFromFormat('Y-m-d H:i:s', $date_tring, Setting::timezone());
//    }

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

        if(is_null($this->date)){
            return null;
        }

        /**
         * Should clone befor do any thing with Carbon datetime obj
         */
        return $this->date->copy()->subHours($hours_before_reservation_timing_send_sms);
    }

    /**
     * Base on notification config: SEND_SMS_TO_CONFIRM_RESERVATION
     * check should send confirm cms
     * @return bool
     */
    public function shouldSendConfirmSMS(){
        $notification_config = Setting::notificationConfig();
        $should_send_sms_to_confirm_reservation = $notification_config(Setting::SEND_SMS_CONFIRMATION) == Setting::SHOULD_SEND;

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
    public function shouldSendSMSOnBooking(){
        $notification_config = Setting::notificationConfig();
        $should_send_sms_on_booking = $notification_config(Setting::SEND_SMS_ON_BOOKING) == Setting::SHOULD_SEND;

        return $should_send_sms_on_booking;
    }

    /**
     * Alias of should send SMS on booking
     * BCS new logic only when reservation RESERVED
     * SMS sent out
     * @return bool
     */
    public function shouldSendSMSOnReserved(){
        return $this->shouldSendSMSOnBooking();
    }

    /**
     * Confirm url for reservation
     * @return string
     */
    public function getConfirmComingUrlAttribute(){
        $confirm_id = $this->confirm_id;

        return route("reservation_confirm", compact('confirm_id'));;
    }
    
    /**
     * Base on deposit config
     * Reservation over specific pax
     * Need pay in advance
     */

    /**
     * Check if reservation require deposit
     * @return bool
     */
    public function requiredDeposit(){
        $deposit_config = Setting::depositConfig();
        $deposit_threshold_pax = $deposit_config(Setting::DEPOSIT_THRESHOLD_PAX);

        if($this->pax_size > $deposit_threshold_pax){
            return true;
        }

        return false;
    }

    /**
     * If require, compute as $deposit property
     * of reservation
     * @return int|mixed
     * @throws \Exception
     */
    public function getDepositAttribute(){
        if($this->requiredDeposit()){
            $deposit_config = Setting::depositConfig();
            $deposit_type   = $deposit_config(Setting::DEPOSIT_TYPE);
            
            $val = 0;
            switch($deposit_type){
                case Setting::FIXED_SUM:
//                    $val = $deposit_config(Setting::FIXED_SUM_VALUE);
                    $val = $deposit_config(Setting::DEPOSIT_VALUE);
                    break;
                case Setting::PER_PAX:
//                    $per_pax_value = $deposit_config(Setting::PER_PAX_VALUE);
                    $per_pax_value = $deposit_config(Setting::DEPOSIT_VALUE);
                    $val = $this->pax_size *  $per_pax_value;
                    break;
            }
            
            return $val;
        }
        
        throw new \Exception('Should not call deposit on reservation which not');
    }

    /**
     * Auto compute send sms confirmation on booot
     * Base on current config
     */
    /**
     * @return int
     */
    public function getSendSMSConfirmationAttribute(){
        $notification_config = Setting::notificationConfig();

        return $notification_config(Setting::SEND_SMS_CONFIRMATION);
    }

    /**
     * Relationship with Outlet
     */
    public function outlet(){
        return $this->hasOne(Outlet::class, 'id', 'outlet_id');
    }

    /**
     * Convenience get outlet name
     */
    public function getOutletNameAttribute(){
        $outlet = $this->outlet;

        return $outlet->outlet_name;
    }

    /**
     * Reservation SMS on booking reserved
     * @return string
     */
    public function getSMSMessageOnReservedAttribute(){
        //send out an SMS
        $long_datetime_str = $this->date->format('M d Y');

        return "Your reservation at $this->outlet_name $long_datetime_str has been received. Reservation code: $this->confirm_id";
    }

    public function getConfirmationSMSMessageAttribute(){
        $minutes_before = Carbon::now(Setting::timezone())->diffInMinutes($this->confirm_sms_date, false);
        /**
         * Bcs of interval loop read database to pop a reservation to send
         * Compute hours before as ceiling round
         */
        $hours_before = ceil($minutes_before / 60);
        $sender_name  = Setting::smsSenderName();
        $time_str     = $this->date->format('H:i');

        $msg  = "You are $hours_before hours from your $sender_name reservation! ";
        $msg .= "$this->adult_pax adults $this->children_pax children at $time_str at $this->outlet_name. ";
        $msg .= "Confirm you are coming: $this->confirm_coming_url";

        return $msg;
    }

    /**
     * Override on serializtion
     * @return array
     */
    public function attributesToArray() {
        $attributes = parent::attributesToArray();

        /**
         * Return as datetime string to consistent with DB
         */
        $attributes['send_confirmation_by_timestamp']
            = $this->send_confirmation_by_timestamp->format('Y-m-d H:i:s');

        return $attributes;
    }
}
