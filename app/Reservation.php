<?php

namespace App;

use Carbon\Carbon;
//use Hashids\Hashids;
use App\Traits\ApiUtils;
use App\Events\ReservationReserved;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
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
 * @see App\Reservation::getConfirmSMSDateAttribute
 *
 * @property mixed $send_confirmation_by_timestamp
 * @see App\Reservation::getSendConfirmationByTimestampAttribute
 * 
 * @property mixed $deposit
 * @see App\Reservation::getDepositAttribute
 *
 * @property mixed send_sms_confirmation
 * @see App\Reservation::getSendSMSConfirmationAttribute
 *
 * Loading through relationship
 * @property mixed $outlet
 * @see App\Reservation::outlet
 * 
 * @property mixed $sms_message_on_reserved
 * @see App\Reservation::getSMSMessageOnReservedAttribute
 * 
 * @property mixed $confirmation_sms_message
 * @see App\Reservation::getConfirmationSMSMessageAttribute
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
//        'full_phone_number',
        'confirm_id',
        'send_confirmation_by_timestamp'
    ];

    protected $hidden = [
        'customer_id',
//        'outlet_id',
        'payment_id',
    ];

    protected $casts = [
        'send_sms_confirmation' => 'boolean',
        'staff_read_state'      => 'boolean'
    ];

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
//        'send_confirmation_by_timestamp',
        'send_sms_confirmation',
        'send_email_confirmation',
        'session_name',
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
//            if(!isset($reservation->attributes['send_confirmation_by_timestamp'])){
//                $reservation->send_confirmation_by_timestamp = $reservation->getSendConfirmationByTimestampAttribute();
//            }

            /**
             * Auto compuate send_sms_confirmation
             * Base on current config
             */
            if(!isset($reservation->attributes['send_sms_confirmation'])){
                $reservation->send_sms_confirmation = $reservation->getSendSMSConfirmationAttribute();
            }

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
            /**
             * @warn may improve in future
             * For checking it should resent SMS
             * When reservation updated info
             */
        });

        static::orderByRerservationTimestamp();
        static::byOutletId();
    }

    /**
     * Validate when create/add/update... on Model
     * @param $reservation_data
     */
    public static function validateOnCRUD($reservation_data){
        $validator = Validator::make($reservation_data, [
            'outlet_id'    => 'required|numeric',
            'salutation'   => 'required',
            'first_name'   => 'required',
            'last_name'    => 'required',
            'email'        => 'required|email',
            'phone_country_code' => 'required|regex:/^\+*(\d{2})/',
            'phone'        => 'required|regex:/\d+$/',
            'adult_pax'    => 'required|numeric',
            'children_pax' => 'required|numeric',
            'reservation_timestamp' => 'required|date_format:Y-m-d H:i:s',
        ]);

        return $validator;
    }

    /**
     * Global scope when get Reservation
     * It should in timeline order
     */
    public static function orderByRerservationTimestamp(){
        static::addGlobalScope('order_by_reservation_timestamp', function(Builder $builder){
            $builder->orderBy('reservation_timestamp', 'dec');
        });
    }

    /**
     * Get Reservation reserved
     * @param $query
     * @return mixed
     */
    public function scopeReservedInDateRange($query){
        $date_range = Setting::dateRange();

        //consider status > RESERVED as booked
        return $query->where([
            ['reservation_timestamp', '>=', $date_range[0]->format('Y-m-d H:i:s')],
            ['reservation_timestamp', '<=', $date_range[1]->format('Y-m-d H:i:s')],
            ['status', '>=', Reservation::RESERVED]
        ]);
    }

    /**
     * Reservation group by date time & capacity
     * Like query into database, concat on different condition
     * >>> can count easily on each group
     *
     * Need count how many reservation at specific datetime & at specific capicity
     * @return mixed
     */
    public static function reservedGroupByDateTimeCapacity(){
        $self = (new Reservation);
        $valid_reservations   = $self->scopeReservedInDateRange($self->query())->get();

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
     * @param string|null $date
     * @return Carbon|null
     */
//    public function getSendConfirmationByTimestampAttribute($date = null){
    public function getSendConfirmationByTimestampAttribute(){
        /**
         * Without outlet_id
         * Can't determine which config used for each outlet
         */
        if(is_null($this->outlet_id)){
            return null;
        }

        session(['outlet_id' => $this->outlet_id]);

        $notification_config = Setting::notificationConfig();
        $hours_before_reservation_timing_send_sms = $notification_config(Setting::HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM);

        /**
         * When default set up send confirmation by timestamp
         * Without reservation_timestamp CAN NOT determine when
         * Ignore
         */
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
//        $notification_config = Setting::notificationConfig();
//        $should_send_sms_to_confirm_reservation = $notification_config(Setting::SEND_SMS_CONFIRMATION) == Setting::SHOULD_SEND;
//
//        return $should_send_sms_to_confirm_reservation;
        return $this->send_sms_confirmation;
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
     * @param $val
     * @return int
     */
//    public function getSendSMSConfirmationAttribute($val = null){
    public function getSendSMSConfirmationAttribute($val = null){
        if(!is_null($val)){
            return $val;
        }

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
        $date_str = $this->date->format('M d Y');
        $time_str = $this->date->format('H:i');

        return "Your reservation at $this->outlet_name on $date_str at $time_str has been received. Reservation code: $this->confirm_id";
    }

    public function getConfirmationSMSMessageAttribute(){
        /**
         * Bcs of interval loop read database to pop a reservation to send
         * May not exactly as what config want
         * Recompute how many hours before
         */
        //$minutes_before = Carbon::now(Setting::timezone())->diffInMinutes($this->confirm_sms_date, false);
        //$hours_before = ceil($minutes_before / 60);
        $hours_before = Carbon::now(Setting::timezone())->diffInHours($this->confirm_sms_date, false);
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
            = $this->getSendConfirmationByTimestampAttribute($attributes['send_confirmation_by_timestamp'])->format('Y-m-d H:i:s');

        return $attributes;
    }

    /**
     * Reservation newer than last 30 days from $start
     * @param $query
     * @param Carbon $start
     * @return
     */
    public function scopeLast30Days($query, Carbon $start = null){
        $start = $start ?: Carbon::now(Setting::timezone());

        $last_30_days = $start->copy()->subDays(30);

        $last_30_days_str = $last_30_days->format('Y-m-d H:i:s');

        return $query->where('reservation_timestamp', '>=', $last_30_days_str);
    }

}
