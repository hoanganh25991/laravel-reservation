<?php

namespace App;

use App\Traits\SendSMS;
use Carbon\Carbon;
use App\Traits\ApiUtils;
use App\Mail\EmailOnVoid;
use App\Traits\ShortenUrl;
use App\Mail\EmailOnCharge;
use App\Traits\CleanString;
use Illuminate\Validation\Rule;
use App\Mail\EmailOnUserCancel;
use App\Events\ReservationReserved;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\PayPalController;
use App\OutletReservationSetting as Setting;

/**
 * @property mixed reservation_timestamp
 * @see protected $dates, which cast data
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
 * @see Reservation::getConfirmComingUrlAttribute
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
 * @property string $payment_id
 * @see App\Reservation::getConfirmationSMSMessageAttribute
 * @property double payment_amount
 * @property string payment_timestamp
 * @property int $payment_status
 * @property string payment_authorization_id
 * @property mixed payment_currency
 * @property mixed paypal_currency
 * @see App\Reservation::getPaypalCurrencyAttribute
 * @method mixed fromToday
 * @see App\Reservation::scopeFromToday
 * @method byDayBetween
 * @see App\Reservation::scopeByDayBetween
 * @method alreadyReserved
 * @see App\Reservation::scopeAlreadyReserved
 * @method notRequiredDeposit
 * @see App\Reservation::scopeNotRequiredDeposit
 * @property mixed confirmation_sms_ask_payment_authorization_message
 * @see App\Reservation::getConfirmationSMSAskPaymentAuthorizationMessageAttribute
 * @property mixed payment_required
 * @see App\Reservation::getConfirmationSMSAskPaymentAuthorizationMessageAttribute
 * @property mixed view_details_url
 * @see App\Reservation::getViewDetailsUrlAttribute
 * @property mixed last_confirm_id
 * @see App\Reservation::getViewDetailsUrlAttribute
 * @property mixed is_edited_by_customer
 * @see App\Reservation::getIsEditedByCustomerAttribute
 * @method namePhoneEmailLikeSearchTerm
 * @see App\Reservation::scopeNamePhoneEmailLikeSearchTerm
 * @property string full_name
 * @see App\Reservation::getFullNameAttribute
 * @property string email_subject
 * @see App\Reservation::getEmailSubjectAttribute
 * @property mixed sms_message_on_user_cancel
 * @see App\Reservation::getSMSMessageOnUserCancelAttribute
 * @property mixed email_subject_on_user_cancel
 * @see App\Reservation::getEmailSubjectOnUserCancelAttribute
 * @property mixed void_msg
 * @see App\Reservation::getVoidMsgAttribute
 * @property mixed charge_msg
 * @see App\Reservation::getChargeMsgAttribute
 * @property string email_content_on_user_cancel
 * @see App\Reservation::getEmailContentOnUserCancelAttribute
 * @property mixed payment_authorization_url
 * @see App\Reservation::getPaymentAuthorizationUrlAttribute
 * @method findSamePhoneSamePax
 * @see App\Reservation::scopeFindSamePhoneSamePax
 * @property mixed edit_url
 * @property mixed no_payment_authorization_sms
 * @property mixed no_confirmation_sms
 * @see App\Reservation::getEditUrlAttribute
 * @property min_hours_in_advance_to_allow_edit
 * @see App\Reservation::getMinHoursInAdvanceToAllowEditAttribute
 */
class Reservation extends HoiModel {

    use ApiUtils;
    use CleanString;
    use SendSMS;

    /**
     * Reservation status
     */
    const REQUIRED_DEPOSIT= 50;
    const AMENDMENTED     = 75;
    const RESERVED        = 100; //init at first
    const REMINDER_SENT   = 200; //sms sent to summary info
    const CONFIRMED       = 300; //remider with CONFIRM link
    const ARRIVED         = 400;
    const USER_CANCELLED  = -100;
    const STAFF_CANCELLED = -200;
    const NO_SHOW         = -300;

    /**
     * Payment status
     */
    const PAYMENT_UNPAID         = 25;
    const PAYMENT_REFUNDED       = 50;
    const PAYMENT_PAID           = 100;
    const PAYMENT_CHARGED        = 200;

    /**
     * Different Url hit to reservation page
     * @see App\Reservation::getEditUrlAttribute
     * @see App\Reservation::getViewDetailsUrlAttribute
     * @see App\Reservation::getConfirmComingUrlAttribute
     * when review:
        http:/<host>?confirmId=HY78AJH&review=true
       when eidt:
        http:/<host>?confirmId=HY78AJH&edit=true
       when payment:
        http:/<host>?confirmId=HY78AJH&payment=true
     */

    protected $table = 'res_reservation';

    protected $guarded = ['id'];

    /**
     * Reservation field should cast to Carbon datetime object
     * Which easy to do math on date
     */
    protected $dates = [
        'reservation_timestamp',
        'payment_timestamp',
    ];

    /**
     * add fields when serialize model
     * base on set/get, these fields computed
     * @see App\Reservation::getConfirmIdAttribute
     */
    protected $appends = [
        'confirm_id',
        'send_confirmation_by_timestamp',
        'deposit',
        'time',
        'paypal_currency',
        'is_edited_by_customer',
        'full_name',
        'min_hours_in_advance_to_allow_edit'
    ];

    /**
     * Should hidden when serialize model
     */
    protected $hidden = [
        'customer_id',
        'payment_id',
    ];

    /**
     * Should cast when serialize model
     */
    protected $casts = [
        'send_sms_confirmation' => 'boolean',
        'staff_read_state'      => 'boolean',
        'phone_country_code'    => 'string',
    ];

    /**
     * Protect model from unwanted column when build query
     */
    protected $fillable = [
        'last_confirm_id',
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
        'send_sms_confirmation',
        'send_email_confirmation',
        'session_name',
        'staff_read_state',
        'payment_id',
        'payment_required',
        'payment_timestamp',
        'payment_amount',
        'payment_currency',
        'payment_status',
        'is_outdoor',
        'no_confirmation_sms',
        'no_payment_authorization_sms',
    ];


    /**
     * Inject into boot process
     * To modify on query scope or
     * Listen eloquent event : creating, saving, updating,...
     */
    protected static function boot() {
        parent::boot();
        
        self::creating(function(Reservation $reservation){
            /**
             * Compute send_sms_confirmation
             * Base on current config
             */
            if(!isset($reservation->attributes['send_sms_confirmation'])){
                $reservation->send_sms_confirmation = $reservation->getSendSMSConfirmationAttribute();
            }

            /**
             * No status explicit bind, setup default
             */
            if(!isset($reservation->attributes['status'])){
                //consider success reserved if no deposit required
                $status = Reservation::RESERVED;
                
                if($reservation->requiredDeposit()){
                    $status = Reservation::REQUIRED_DEPOSIT;

                    //implicit tell payment_status as unpaid
                    $reservation->payment_status = Reservation::PAYMENT_UNPAID;
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
             * @should resent SMS
             * To info customer what updated
             */
            $previous_state = $reservation->getOriginal('status');
            //Only handle when state change
            if($previous_state == Reservation::REQUIRED_DEPOSIT){

                switch($reservation->status){
                    case Reservation::RESERVED:
                        event(new ReservationReserved($reservation));
                        break;
                    default:
                        break;
                }

                //when return as false
                //we explicit tell discard save record to DB
                return true;
            }
        });

        self::saving(function(Reservation $reservation){
            /**
             * It's time to de refund/charge
             * When status change
             */
            /**
             * How to compare if it changed
             * PAID > REFUNDED
             * PAID > CHARGED
             */
            $previous_state = $reservation->getOriginal('payment_status');
            //Only handle when state change
            if($previous_state == Reservation::PAYMENT_PAID){
                $transaction_id = $reservation->payment_id;
                
                $success = false;
                switch($reservation->payment_status){
                    case Reservation::PAYMENT_REFUNDED:
                        $success = PayPalController::void($transaction_id);
                        break;
                    case Reservation::PAYMENT_CHARGED:
                        $success = PayPalController::charge($transaction_id);
                        break;
                    default:
                        break;
                }
                
                if(!$success){
                    //restore status
                    $reservation->payment_status = $previous_state;
                }
                
                //when return as false
                //we explicit tell discard save record to DB
                return true;
            }
        });

        static::orderByRerservationTimestamp();
        static::byOutletId();
        static::notAmended();
    }

    protected static function notAmended(){
        static::addGlobalScope('not_amended', function (Builder $builder){
            $builder->where('status', '!=', Reservation::AMENDMENTED);
        });
    }
    
    /**
     * Validate when create/add/update...
     * @param array $reservation_data
     * @return \Illuminate\Validation\Validator
     */
    public static function validateOnCRUD($reservation_data){
        $allowed_outltes_id = Outlet::all()->pluck('id')->toArray();
        
        $validator = Validator::make($reservation_data, [
            'outlet_id'    => ['required', 'numeric', Rule::in($allowed_outltes_id)],
            'adult_pax'    => 'required|numeric',
            'children_pax' => 'required|numeric',
            'reservation_timestamp' => 'required|date_format:Y-m-d H:i:s',
            'salutation'   => 'required',
            'first_name'   => 'required',
            'last_name'    => 'required',
            'email'        => 'required|email',
            'phone_country_code' => 'required|regex:/\d{2,}$/',
            'phone'        => 'required|regex:/\d{6,}/',
        ]);

        return $validator;
    }

    /**
     * In admin page, email is NOT a required field
     * To make reservation
     * @param $reservation_data
     * @return mixed
     */
    public static function validateOnCRUDInAdminPage($reservation_data){
        $allowed_outltes_id = Outlet::all()->pluck('id')->toArray();

        $validator = Validator::make($reservation_data, [
            'outlet_id'    => ['required', 'numeric', Rule::in($allowed_outltes_id)],
            'adult_pax'    => 'required|numeric',
            'children_pax' => 'required|numeric',
            'reservation_timestamp' => 'required|date_format:Y-m-d H:i:s',
            'salutation'   => 'required',
            'first_name'   => 'required',
            'last_name'    => 'required',
            'phone_country_code' => 'required|regex:/\d{2,}$/',
            'phone'        => 'required|regex:/\d{6,}/',
        ]);

        return $validator;
    }

    /**
     * Global query scope, order by reservation time
     */
    public static function orderByRerservationTimestamp(){
        static::addGlobalScope('order_by_reservation_timestamp', function(Builder $builder){
            $builder->orderBy('reservation_timestamp', 'asc');
        });
    }

    /**
     * Get reservation reserved in date range
     * @see App\OutletReservationSetting::dateRange
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
     * Count how many reservation at specific datetime & capicity
     *
     * Group reservation by date time & capacity
     * Count members of each group
     *
     * Like query into database, concat column name of different conditions to run GROUPBY
     *
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
     * Timestamp accept both as number or string as date-timestamp
     * When number, convert it back
     * @param number|string $value
     */
    public function setReservationTimestampAttribute($value){
        $db_timestamp = $value;
        // Case submit timestamp as seconds
        if(is_numeric($value)){
            $date_time = Carbon::createFromTimestamp((int)$value, Setting::timezone());
            $db_timestamp = $date_time->format('Y-m-d H:i:s');
        }

        $this->attributes['reservation_timestamp'] = $db_timestamp;
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
     * Hash reservaion id to generate confirm id
     * Hide reservation id from customer
     * @return string
     */
    public function getConfirmIdAttribute(){
        if(!is_null($this->last_confirm_id)){
            return $this->last_confirm_id;
        }

        $id         = $this->id;
        $confirm_id = Setting::hash()->encode($id);
        
        return $confirm_id;
    }
    
    public function getFullPhoneNumberAttribute(){
        return "{$this->phone_country_code}{$this->phone}";
    }

    /**
     * When should send confirmation (SMS, email,...)
     * Base on notification config: HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM
     * @return Carbon|null
     * @throws \Exception
     */
    public function getSendConfirmationByTimestampAttribute(){
        //Without outlet_id, can't determine which config used
        if(is_null($this->outlet_id)){
            throw new \Exception('Reservation need outlet_id to decide config');
        }

        //Setting::injectOutletId($this->outlet_id);

        $notification_config = Setting::notificationConfig($this->outlet_id);
        $hours_before_reservation_timing_send_sms = $notification_config(Setting::HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM);

       //Without reservation_timestamp CAN NOT determine when
        if(is_null($this->date)){
            return null;
        }

        return $this->date->copy()->subHours($hours_before_reservation_timing_send_sms);
    }

    /**
     * Base on notification config: SEND_SMS_TO_CONFIRM_RESERVATION
     * check should send confirm cms
     * @return bool
     */
    public function shouldSendConfirmSMS(){
        return $this->send_sms_confirmation;
    }

    /**
     * Should send reservaiotn summary SMS on booking
     * Base on notification config: SEND_SMS_ON_BOOKING
     * @return bool
     */
    public function shouldSendSMSOnBooking(){
        $notification_config = Setting::notificationConfig();
        $should_send_sms_on_booking = $notification_config(Setting::SEND_SMS_ON_BOOKING) == Setting::SHOULD_SEND;

        return $should_send_sms_on_booking;
    }

    /**
     * Should send reservaiotn email on booking
     * Base on notification config: SEND_SMS_ON_BOOKING
     * @return bool
     */
    public function shouldSendEmailOnBooking(){
        $notification_config = Setting::notificationConfig();
        $should_send_email_on_booking = $notification_config(Setting::SEND_EMAIL_ON_BOOKING) == Setting::SHOULD_SEND;

        return $should_send_email_on_booking;
    }

    /**
     * Alias of should send SMS on booking
     * @return bool
     */
    public function shouldSendSMSOnReserved(){
        return $this->shouldSendSMSOnBooking();
    }

    /**
     * Confirm url for reservation
     * @return string
     * @throws \Exception
     */
    public function getConfirmComingUrlAttribute(){
        $confirm_id = $this->confirm_id;
        //$url        = route('reservation_confirm', compact('confirm_id'));
        $base_url     = env('APP_URL');

        if(is_null(env('APP_URL'))){throw new \Exception('Please submit APP_URL in .env to build confirm coming url for reservation');};

        $endWithSlash = substr($base_url, -1) == '/';
        $base_url     = $endWithSlash ? substr($base_url, 0, strlen($base_url) - 1) : $base_url;
        $url          = "$base_url/?confirmId=$confirm_id";
        $short_url    = ShortenUrl::make($url);

        if(!is_null($short_url)){
            $url    = $short_url;
        }

        return $url;
    }

    /**
     * Payment Authorization url for reservation
     * @return string
     * @throws \Exception
     */
    public function getPaymentAuthorizationUrlAttribute(){
        $confirm_id = $this->confirm_id;
        //$url        = route('reservation_confirm', compact('confirm_id'));
        $base_url     = env('APP_URL');

        if(is_null(env('APP_URL'))){throw new \Exception('Please submit APP_URL in .env to build confirm coming url for reservation');};

        $endWithSlash = substr($base_url, -1) == '/';
        $base_url     = $endWithSlash ? substr($base_url, 0, strlen($base_url) - 1) : $base_url;
        $url          = "$base_url/?confirmId=$confirm_id&payment=true";
        $short_url    = ShortenUrl::make($url);

        if(!is_null($short_url)){
            $url    = $short_url;
        }

        return $url;
    }

    /**
     * View detail url
     */
    public function getViewDetailsUrlAttribute(){
        $confirm_id = $this->confirm_id;
        //$url        = route('reservation_confirm', compact('confirm_id'));
        $base_url     = env('APP_URL');

        if(is_null(env('APP_URL'))){throw new \Exception('Please submit APP_URL in .env to build confirm coming url for reservation');};

        $endWithSlash = substr($base_url, -1) == '/';
        $base_url     = $endWithSlash ? substr($base_url, 0, strlen($base_url) - 1) : $base_url;
        $url          = "$base_url/?confirmId=$confirm_id&review=true";
        $short_url    = ShortenUrl::make($url);

        if(!is_null($short_url)){
            $url    = $short_url;
        }

        return $url;
    }

    /**
     * Edit url
     */
    public function getEditUrlAttribute(){
        $confirm_id = $this->confirm_id;
        //$url        = route('reservation_confirm', compact('confirm_id'));
        $base_url     = env('APP_URL');

        if(is_null(env('APP_URL'))){throw new \Exception('Please submit APP_URL in .env to build confirm coming url for reservation');};

        $endWithSlash = substr($base_url, -1) == '/';
        $base_url     = $endWithSlash ? substr($base_url, 0, strlen($base_url) - 1) : $base_url;
        $url          = "$base_url/?confirmId=$confirm_id&edit=true";
        $short_url    = ShortenUrl::make($url);

        if(!is_null($short_url)){
            $url    = $short_url;
        }

        return $url;
    }
    
    /**
     * Need pay in advance
     * When reservation over specific pax
     * Base on deposit config : DEPOSIT_THRESHOLD_PAX
     * Add admin_wish, which override default config
     */

    /**
     * Check if reservation require deposit
     * @return bool
     */
    public function requiredDeposit(){
        // Allow check requiredDeposit as admin want
        // When he create booking inside admin page
        // admin wish override on default config
        $admin_wish = $this->payment_required;
        if(!is_null($admin_wish)){
            return $admin_wish;
        }

        $deposit_config = Setting::depositConfig($this->outlet_id);
        $deposit_threshold_pax = $deposit_config(Setting::DEPOSIT_THRESHOLD_PAX);

        $required = $this->pax_size > $deposit_threshold_pax;

        return $required;
    }

    /**
     * If require, compute as $deposit property
     * Now deposit will rely on payment_amount also
     * When payment_amount set > use it override on default config
     * @return int|mixed
     * @throws \Exception
     */
    public function getDepositAttribute(){
        if($this->requiredDeposit()){
            $payment_amount = $this->payment_amount;
            if(!is_null($payment_amount)){
                return $payment_amount;
            }
            //inject which outlet_id use to get config
            //Setting::injectOutletId($this->outlet_id);
            $deposit_config = Setting::depositConfig($this->outlet_id);
            $deposit_type   = $deposit_config(Setting::DEPOSIT_TYPE);
            
            $val = 0;
            switch($deposit_type){
                case Setting::FIXED_SUM:
                    $val = $deposit_config(Setting::DEPOSIT_VALUE);
                    break;
                case Setting::PER_PAX:
                    $per_pax_value = $deposit_config(Setting::DEPOSIT_VALUE);
                    $val = $this->pax_size *  $per_pax_value;
                    break;
            }
            
            return $val;
        }
        
        //throw new \Exception('Should not call deposit on reservation which not required');
        return null;
    }

    /**
     * Auto compute send sms confirmation on booot
     * Base on current config
     * @param $val
     * @return int
     */
    public function getSendSMSConfirmationAttribute($val = null){
        //if send sms confirmation setup, return it
        if(!is_null($val)){
            return $val;
        }
        //if not, return default from current config
        $notification_config = Setting::notificationConfig($this->outlet_id);
        return $notification_config(Setting::SEND_SMS_CONFIRMATION);
    }

    /**
     * Relationship with Outlet
     * @return Outlet
     */
    public function outlet(){
        return $this->hasOne(Outlet::class, 'id', 'outlet_id')->withoutGlobalScope('brand_id');
    }

    /**
     * Get outlet name
     */
    public function getOutletNameAttribute(){
        $outlet = $this->outlet;

        return $outlet->outlet_name;
    }

    /**
     * SMS message on booking reserved
     * @return string
     */
    public function getSMSMessageOnReservedAttribute(){
        //send out an SMS
        $date_str = $this->date->format('d M Y');
        $time_str = $this->date->format('H:i');

        $msg = "Your reservation at $this->outlet_name on $date_str at $time_str has been received. Reservation No. $this->confirm_id. ";
        $msg .= "View your reservation details: $this->view_details_url";

        return $msg;
    }

    /**
     * SMS message when reminder, with confirmation link
     * @return string
     */
    public function getConfirmationSMSMessageAttribute(){
        /**
         * Bcs of interval loop read database to pop a reservation to send
         * May not exactly as what config want
         * Recompute how many hours before
         */
        $hours_before = Carbon::now(Setting::timezone())->diffInHours($this->reservation_timestamp, false);
        $sender_name  = Setting::smsSenderName();
        $time_str     = $this->date->format('H:i');

        $msg  = "You are $hours_before hours from your $sender_name reservation";

        //how many pax
        if ($this->adult_pax > 0 && $this->children_pax > 0)
            $msg .= " for $this->adult_pax adults, $this->children_pax children";
        else if ($this->children_pax > 0)
            $msg .= " for $this->children_pax children";
        else
            $msg .= " for $this->adult_pax adults";

        $msg .= " at $time_str at $this->outlet_name. ";
        $msg .= "Reservation No. $this->confirm_id. ";
        $msg .= "Please confirm that you are coming $this->confirm_coming_url";

        return $msg;
    }

    /** SMS message when admin create booking for customer in side admin reservations page */
    public function getConfirmationSMSAskPaymentAuthorizationMessageAttribute(){
        //send out an SMS
        $date_str = $this->date->format('d M Y');
        $time_str = $this->date->format('H:i');

        $msg = "Your reservation is not yet confirmed! To complete your reservation at $this->outlet_name on $date_str at $time_str";

        //how many pax
        if ($this->adult_pax > 0 && $this->children_pax > 0)
            $msg .= " for $this->adult_pax adults, $this->children_pax children";
        else if ($this->children_pax > 0)
            $msg .= " for $this->children_pax children";
        else
            $msg .= " for $this->adult_pax adults";

        // Add punctation
        $msg .= ". ";
        $msg .= "Please make a credit card authorization via the following link: $this->payment_authorization_url";

        return $msg;
    }

    /**
     * Override on serialization process
     * @return array
     */
    public function attributesToArray() {
        $attributes = parent::attributesToArray();

        //conver Carbon dateime obj to timestamp str
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

        $last_30_days     = $start->copy()->subDays(30);
        $last_30_days_str = $last_30_days->format('Y-m-d H:i:s');

        return $query->where('reservation_timestamp', '>=', $last_30_days_str);
    }

    /**
     * Reservation newer than last 30 days from $start
     * @param $query
     * @param Carbon $start
     * @return
     */
    public function scopeFromToday($query, Carbon $start = null){
        // $start default as 'the start of today'
        // $start as now >>> reservation just 10 minutes ago not listed in
        // set his time back to 0 hours 0 minutes 0 seconds
        $start = $start ?: Carbon::now(Setting::timezone())->setTime(0, 0, 0);

        $today_str = $start->format('Y-m-d H:i:s');

        return $query->where('reservation_timestamp', '>=', $today_str);
    }

    /**
     * Send sms base on nexmo require phone_code as '+xx', start with +
     * Support transform code as '65' > '+65'
     * Or empty '' > '+65'
     * @param $value
     */
    public function setPhoneCountryCodeAttribute($value){
        $code = $value;

        $start_with_signal_plus = substr($value, 0, 1) == '+';
        if(!$start_with_signal_plus){
            $code = "+$code";
        }

        $this->attributes['phone_country_code'] = $code;
    }

    /**
     * When payment authorization case happen
     * Need this info to help create paypal popup dynamic with right currency
     * Reservation itself doesn't have these info
     * This come from Outlet Setting
     */
    public function getPaypalCurrencyAttribute(){

        $outlet_id = $this->outlet_id;

        if(!$outlet_id){
            throw new \Exception("Reservation $this->id, doesnt have outlet_id info");
        }

        $deposit_config = Setting::depositConfig($outlet_id);

        return $deposit_config(Setting::PAYPAL_CURRENCY);
    }

    /**
     * Customer input phone allow start with 0
     * Auto trunk this leading 0 when save to database
     * @param $value
     */
    public function setPhoneAttribute($value){
        $phone = $value;

        $start_with_zero = substr($value, 0, 1) == '0';

        if($start_with_zero){
            $phone = substr($value, 1);
        }

        // Notice: different from get which return as result
        // To set an attribute on model, have to explicit tell set
        // Return will let to NOOO CHANGE on model phone field
        $this->attributes['phone'] = $phone;
    }

    /**
     * Support query reservations by day
     * @param $query
     * @param Carbon $star_day
     * @param Carbon $end_day
     */
    public function scopeByDayBetween($query, $star_day, $end_day){
        return $query->where([
            // lager than & equal
            ['reservation_timestamp', '>=', $star_day->format('Y-m-d H:m:s')],
            // less than
            ['reservation_timestamp', '<',   $end_day->format('Y-m-d H:m:s')],
        ]);
    }

    public function scopeAlreadyReserved($query){
        return $query->where('status', '>=', Reservation::RESERVED);
    }

    /**
     * This function helps to fitler out reservation as AMENDMENTED
     * Only show to customer, reservation completely booked
     * @param $query
     * @return mixed
     */
    public function scopeNotRequiredDeposit($query){
        return $query->where([
//            ['status', '!=', Reservation::REQUIRED_DEPOSIT],
//            ['status', '!=', Reservation::AMENDMENTED],
        ]);
        return $query;
    }

	public function getMinHoursInAdvanceToAllowEditAttribute(){
		$buffer_config = Setting::bufferConfig($this->outlet_id);
		$min_hours_customer_allowed_edit = $buffer_config(Setting::MIN_HOURS_IN_ADVANCE_TO_ALLOW_CANCELLATION_AMENDMENT_PRIOR_TO_RESERVATION_TIME);
		return $min_hours_customer_allowed_edit;
	}

    /**
     * Check if with current condition of setting
     * With current booking made by customer
     * Can he make an amendment?
     * @return bool
     */
    public function allowedEditByCustomer(){
        return Setting::isCustomerAllowedToEditReservation($this);
    }

    /**
     * Find Reservation through confirm id
     * Many place use it
     * So now write it down in Reservation Model
     * @param $confirm_id
     * @return Reservation
     * @throws \Exception
     */
    public static function findByConfirmId($confirm_id){
        // Try to parse the confirm_id
        try{ $reservation_id = Setting::hash()->decode($confirm_id);
        }catch(\Exception $e){throw new \Exception("Sorry, confirm id is invalid.");}

        $bag = compact('confirm_id', 'reservation_id');
        // Find reservation base on id
        /** @var Reservation $reservation */
        $reservation = Reservation::withoutGlobalScopes()
            // Find reservation status <> AMENDMENTED
            ->where('status', '!=', Reservation::AMENDMENTED)
            ->where(function($query) use ($bag){
                // Try compare as id or last_confirm_id
                $query->where('id', $bag['reservation_id'])
                      ->orWhere('last_confirm_id', $bag['confirm_id']);
            })->first();

        if(is_null($reservation)){
            throw new \Exception("Sorry, we cant find your reservation.");
        }

        return $reservation;
    }

    public static function findByConfirmIdScopeOutlet($confirm_id){
        // Try to parse the confirm_id
        try{ $reservation_id = Setting::hash()->decode($confirm_id);
        }catch(\Exception $e){throw new \Exception("Sorry, confirm id is invalid.");}

        $bag = compact('confirm_id', 'reservation_id', 'outlet_id');
        // Find reservation base on id
        /** @var Reservation $reservation */
        $reservation = Reservation::where('status', '!=', Reservation::AMENDMENTED)
            // Reservation with global scope > affected by outlet_id scope
            ->where(function($query) use ($bag){
                // Try compare as id or last_confirm_id
                $query->where('id', $bag['reservation_id'])
                    ->orWhere('last_confirm_id', $bag['confirm_id']);
            })->first();

        if(is_null($reservation)){
            throw new \Exception("Sorry, we cant find your reservation.");
        }

        return $reservation;
    }

    /**
     * Refund in case customer edit this reservation
     * Create completely new one
     */
    public function refundWhenPaymentAlreadyPaid(){
        $payment_authorization_paid = $this->payment_status == Reservation::PAYMENT_PAID;
        if($payment_authorization_paid){
            // Auto void it
            $transaction_id = $this->payment_id;
            $success = PayPalController::voidBcsCustomerEditReservation($transaction_id);
            if($success){
                // Obmit event is good, BUT, obmit in same thread code
                // Lead to other code be affected
                // Reservation::flushEventListeners();
                // Play a cheat on
                $this->payment_status = -100;
                $this->syncOriginal();
                $this->payment_status = Reservation::PAYMENT_REFUNDED;
                $this->save();
            }else{
                // What should do when refund fail?
                Log::info("Customer edit reservation, BUT refund on authorization reservation fail. Confirm id: $this->confirm_id");
            }
        }
    }

    /**
     * Check if customer already edit on this reservation
     * If he did, reservation has 'last_confirm_id;, which point to confirm_iD of last reservation
     *
     * @logic edit on reservation means create a new one with SAME confirm_id
     * @return bool
     */
    public function getIsEditedByCustomerAttribute(){
        $has_last_confirm_id = !is_null($this->last_confirm_id);

        $is_edited_by_customer = $has_last_confirm_id;

        return $is_edited_by_customer;
    }

    /**
     * Find in database, which reservation by customer info
     * Name, Phone or Email
     * Limit as 15 one
     * @param $query
     * @param $term
     * @return mixed
     */
    public function scopeNamePhoneEmailLikeSearchTerm($query, $term){
        $clean_term = trim($term);
        return $query->where('first_name', 'LIKE', "%$clean_term%")
            ->orWhere('last_name', 'LIKE', "%$clean_term%")
            ->orWhere('phone', 'LIKE', "%$clean_term%")
            ->orWhere('email', 'LIKE', "%$clean_term%")
            ->fromLast3Days()
            ->skip(0)
            ->take(30);
    }
    
    public function getFullNameAttribute(){
        $full_name = "$this->salutation $this->first_name $this->last_name";
        return $full_name;
    }

    public function getEmailSubjectAttribute(){
        $date_time = $this->date->format('dS F');
        $subject   = "Your $this->outlet_name Reservation On $date_time";
        return $subject;
    }

    public function getEmailSubjectOnUserCancelAttribute(){
        $date_str = $this->date->format('dS F');
        $subject  = "You Have Cancelled Your $this->outlet_name Reservation on $date_str";

        return $subject;
    }
    
    public function getEmailContentOnUserCancelAttribute(){
        $date_str = $this->date->format('dS F');
        $time_str = $this->date->format('H:i a');

        $msg = "Your reservation";
        //how many pax
        if ($this->adult_pax > 0 && $this->children_pax > 0)
            $msg .= " for $this->adult_pax adults, $this->children_pax children";
        else if ($this->children_pax > 0)
            $msg .= " for $this->children_pax children";
        else
            $msg .= " for $this->adult_pax adults";

        $msg .= " on $date_str at $time_str has been cancelled.";
        
        return $msg;
    }

    public function sendSMSEmailConfirmWhenUserCancel(){
        $this->sendSMSWhenUserCancel();
        $this->sendEmailWhenUserCancel();
    }

    public function getSMSMessageOnUserCancelAttribute(){
        //send out an SMS
        $date_str = $this->date->format('dS F Y');
        $time_str = $this->date->format('H:i a');

        $msg = "Your reservation at $this->outlet_name on $date_str at $time_str has been cancelled.";

        $payment_authorization_paid = $this->payment_status == Reservation::PAYMENT_PAID;
        $is_voided = $this->requiredDeposit() && $payment_authorization_paid;
        if($is_voided){
            $amount = "$this->deposit ($this->payment_currency)";
            $msg .= " Additionally, we have voided your earlier payment authorisation of $amount.";
        }

        return $msg;
    }

    /**
     * Send Email
     * At different state, we send to customer email base on reservation info
     * All these functions should in Reservation Model
     */

    /**
     * Send Email when customer click cancel
     * @return bool
     */
    public function sendEmailWhenUserCancel(){
        $customer_name = "$this->salutation $this->first_name $this->last_name";
        $customer      = (object)['email' => $this->email, 'name' => $customer_name];

        // Send SMS
        try{
            Mail::to($customer)->send(new EmailOnUserCancel($this));
            $success_sent = true;
        }catch(\Exception $e){
            $success_sent = false;
            $msg = "Fail to send email on user cancel. ";
            $exception_msg = $e->getMessage();
            $msg .= "$exception_msg.";
            Log::info($msg);
        }

        return $success_sent;
    }

    /**
     * Send Email when staff void a payment in reservation
     * @return bool
     */
    public function sendEmailVoidEmail(){
        $customer_name = "$this->salutation $this->first_name $this->last_name";
        $customer      = (object)['email' => $this->email, 'name' => $customer_name];
        
        try{
            Mail::to($customer)->send(new EmailOnVoid($this));
            $success_sent = true;
        }catch(\Exception $e){
            $success_sent = false;
        }
        
        return $success_sent;
    }

    /**
     * Send Email when staff charge a payment in reservation
     * @return bool
     */
    public function sendEmailChargeEmail(){
        $customer_name = "$this->salutation $this->first_name $this->last_name";
        $customer      = (object)['email' => $this->email, 'name' => $customer_name];
        
        try{
            Mail::to($customer)->send(new EmailOnCharge($this));
            $success_sent = true;
        }catch(\Exception $e){
            $success_sent = false;
        }
        
        return $success_sent;
    }

    /**
     * Send SMS when customer cancel
     * @return bool
     */
    public function sendSMSWhenUserCancel(){
        $telephone    = $this->full_phone_number;
        $message      = $this->sms_message_on_user_cancel;
        $sender_name  = Setting::smsSenderName();
        $success_sent = $this->sendOverNexmo($telephone, $message, $sender_name);

        if(!$success_sent){
            $msg = "Fail to send SMS on user cancel. ";
            $msg .= $success_sent;

            Log::info($msg);
        }

        return $success_sent;
    }

    /**
     * Send SMS when ask customer complete payment
     * @return bool
     */
    public function sendSMSPaymentAuthorizationRequest(){
        $telephone    = $this->full_phone_number;
        $message      = $this->confirmation_sms_ask_payment_authorization_message;
        $sender_name  = Setting::smsSenderName();
        $success_sent = $this->sendOverNexmo($telephone, $message, $sender_name);

        if($success_sent){
            // Increase count payment_authorization sms
            $curr_no_sms = $this->no_payment_authorization_sms;
            $no_sms = $curr_no_sms + 1;
            // Save
            $this->no_payment_authorization_sms = $no_sms;
            $this->save();
        }

        return $success_sent;
    }

    /**
     * Send SMS when customer success booking
     * @return bool
     */
    public function sendSMSReservationReserved(){
        $telephone    = $this->full_phone_number;
        $message      = $this->sms_message_on_reserved;
        $sender_name  = Setting::smsSenderName();
        $success_sent = $this->sendOverNexmo($telephone, $message, $sender_name);

        if($success_sent){}

        return $success_sent;
    }

    /**
     * Send SMS remind customer X hours before reservation time
     * @return bool
     */
    public function sendSMSConfirmationMsg(){
        $telephone    = $this->full_phone_number;
        $message      = $this->confirmation_sms_message;
        $sender_name  = Setting::smsSenderName();

        // Only sent confirmation sms, when customer already complete booking process
        $reserved    = $this->status >= Reservation::RESERVED;
        if(!$reserved) return false;

        $success_sent = $this->sendOverNexmo($telephone, $message, $sender_name);;
        if($success_sent){
            // Only update status, when current one is not reach reminder_sent
            // And reservation completely reserved
            $last_status = $this->status;
            $reserved    = $this->status >= Reservation::RESERVED;
            $less_then_reminder_sent = $last_status <= Reservation::REMINDER_SENT;
            if($reserved && $less_then_reminder_sent){
                $this->status = Reservation::REMINDER_SENT;
                $curr_no = $this->no_confirmation_sms;
                $no_sms  = $curr_no + 1;
                $this->no_confirmation_sms = $no_sms;
                $this->save();
            }
        }

        return $success_sent;
    }

    /**
     * Send SMS when staff void payment in reservation
     * @return bool
     */
    public function sendSMSVoidMsg(){
        $telephone   = $this->full_phone_number;
        $message     = $this->void_msg;
        $sender_name = Setting::smsSenderName();

        $success_sent = $this->sendOverNexmo($telephone, $message, $sender_name);

        if($success_sent){}

        return $success_sent;
    }

    /**
     * Send SMS when staff charge payment in reservation
     * @return bool
     */
    public function sendSMSChargeMsg(){
        $telephone   = $this->full_phone_number;
        $message     = $this->charge_msg;
        $sender_name = Setting::smsSenderName();

        $success_sent = $this->sendOverNexmo($telephone, $message, $sender_name);

        if($success_sent){}

        return $success_sent;
    }

    public function scopeFromLast3Days($query){
        $last_3_days = Carbon::now(Setting::timezone())->setTime(0, 0, 0)->subDays(3);
        return $query->where('reservation_timestamp', '>=', $last_3_days->format('Y-m-d H:i:s'));
    }

    public function getVoidMsgAttribute(){
        $amount = "$this->deposit ($this->payment_currency)";
        $msg = "Dear customer, this is to inform you that we have voided your earlier payment authorisation of $amount";
        return $msg;
    }

    public function getChargeMsgAttribute(){
        $amount = "$this->deposit ($this->payment_currency)";
        $msg = "Dear customer, due to our no-show policy, we have exercised your payment authorisation of $amount";
        return $msg;
    }

    /**
     * @param $query
     * @param $tmpReservation Reservation
     * @return mixed
     */
    public function scopeFindSamePhoneSamePax($query, $tmpReservation){
        $now = Carbon::now(Setting::timezone());
        $yesterday = $now->copy()->addDays(-1)->setTime(0, 0, 0);
        $yesterday_str = $yesterday->format('Y-m-d H:i:s');

        return $query->where('phone_country_code', $tmpReservation->phone_country_code)
                     ->where('phone', $tmpReservation->phone)
                     ->where('adult_pax', $tmpReservation->adult_pax)
                     ->where('children_pax', $tmpReservation->children_pax)
                     ->where('reservation_timestamp', $tmpReservation->reservation_timestamp)
                     ->where('created_timestamp', '>=', $yesterday_str);
    }

    /**
     * @param $query
     * @param $tmpReservation Reservation
     * @return mixed
     */
    public function scopeFindCustomerByPhone($query, $tmpReservation){
        return $query->where('phone', 'LIKE',  "%$tmpReservation->phone%")
                     ->where('phone_country_code', 'LIKE', "%$tmpReservation->phone_country_code%")
                     ->orderBy('reservation_timestamp', 'dsc');
    }

    /**
     * @param $query
     * @param $tmpReservation Reservation
     * @return mixed
     */
    public function scopeFindCustomerByPhoneNotEmptyEmail($query, $tmpReservation){
        return $query->where('phone', 'LIKE',  "%$tmpReservation->phone%")
                     ->where('phone_country_code', 'LIKE', "%$tmpReservation->phone_country_code%")
                     ->whereNotNull('email')
                     ->where('email', '<>', '')
                     ->orderBy('reservation_timestamp', 'dsc');
    }

}
