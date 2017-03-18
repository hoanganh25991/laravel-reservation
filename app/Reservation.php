<?php

namespace App;

use Carbon\Carbon;
use Hashids\Hashids;
use App\Traits\ApiUtils;
use App\Events\ReservationCreated;
use App\OutletReservationSetting as Setting;

/**
 * @property mixed reservation_timestamp
 * @property Carbon date
 * @property mixed adult_pax
 * @property mixed children_pax
 * @property mixed pax_size
 * @property mixed id
 * @property mixed status
 * @property mixed date_string
 * @property mixed confirm_id
 * @property mixed phone_country_code
 * @property mixed phone
 * @property mixed full_phone_number
 * @property Carbon confirm_SMS_date
 * @property string outlet_name
 * @property string confirm_comming_url
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


    public function scopeValidInDateRange($query){
        $date_range = $this->availableDateRange();

        //consider status > CONFIRMED as booked
        return $query->where([
            ['reservation_timestamp', '>=', $date_range[0]->format('Y-m-d H:i:s')],
            ['reservation_timestamp', '<=', $date_range[1]->format('Y-m-d H:i:s')],
            ['status', '>=', Reservation::CONFIRMED] 
        ]);
    }
    
    protected function validGroupByDateTimeCapacity(){
        $valid_reservations   = Reservation::validInDateRange()->get();

        $capacity_counted_reservations =
            $valid_reservations->map(function($reservation){
                                    $date  = $reservation->date->format('Y-m-d');
                                    $time  = $reservation->date->format('H:i');
                                    $capacity   = Timing::getCapacityName($reservation->pax_size);

                                    return static::getGroupNameByDateTimeCapacity($date, $time, $capacity);
                                })
                                ->groupBy(function($group_name){return $group_name;})
                                ->map(function($group){return $group->count();});

        return $capacity_counted_reservations;
    }
    
    public static function getGroupNameByDateTimeCapacity($date, $time, $capacity){
        return "{$date}_{$time}_{$capacity}";
    }
    
    public function getDateAttribute(){
        return $this->reservation_timestamp;
    }

    public function getPaxSizeAttribute(){
        return ($this->adult_pax + $this->children_pax);
    }

    public function getReservationTimestampAttribute($date_tring){
        return Carbon::createFromFormat('Y-m-d H:i:s', $date_tring, Setting::timezone());
    }

    public function getCapacityNameAttribute(){
        return "capacity_x";
    }

    protected static function boot() {
        parent::boot();

        static::byOutletId();
    }

    public function getConfirmIdAttribute(){
        $id = $this->id;
//        $hashids = new Hashids(Setting::HASH_SALT, 5);
        $hashids = new Hashids('', 7);
        $confirm_id = $hashids->encode($id);
        
        return $confirm_id;
    }
    
    public function getFullPhoneNumberAttribute(){
        return "{$this->phone_country_code}{$this->phone}";
    }

    public function getConfirmSMSDateAttribute(){
        $notification_config = Setting::notificationConfig();
        $hours_before_reservation_timing_send_sms = $notification_config('HOURS_BEFORE_RESERVATION_TIME_TO_SEND_SMS');
        
        return $this->date->subHours($hours_before_reservation_timing_send_sms);
    }

    public function getConfirmComingUrlAttribute(){
        $confirm_id = $this->confirm_id;

        return url("reservations/$confirm_id");
    }
}
