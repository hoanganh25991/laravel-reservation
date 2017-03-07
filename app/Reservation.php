<?php

namespace App;

use Carbon\Carbon;
use Hashids\Hashids;
use App\Traits\ApiUtils;
use App\OutletReservationSetting as Setting;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property mixed reservation_timestamp
 * @property static date
 * @property mixed adult_pax
 * @property mixed children_pax
 * @property mixed pax_size
 * @property mixed id
 * @property mixed status
 * @property mixed date_string
 */
class Reservation extends HoiModel {

    use ApiUtils;


    /**
     * Reservation status
     */
    const RESERVED        = 100;
    const REMINDER_SENT   = 200;
    const CONFIRMED       = 300;
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

        $reservations_by_date_by_time_by_capacity =
            $valid_reservations
                ->groupBy(function($r){return $r->date->format('Y-m-d');})
                ->map->groupBy(function($r){return $r->date->format('H:i');})
                ->map->map->map->groupBy(function($r){return Timing::getCapacityName($r->pax_size);});

        $capacity_counted_reservations =
            $reservations_by_date_by_time_by_capacity
                ->map->map->map->map(function($g){return $g->count();});
        //dd($capacity_counted_reservations);

        return $capacity_counted_reservations;
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

        $outlet_id = session('outlet_id');

        if(!is_null($outlet_id)){
            static::addGlobalScope('base on outlet', function (Builder $builder) use($outlet_id){
                $builder->where('outlet_id', $outlet_id);
            });
        }
    }

    public function getConfirmIdAttribute(){
        $id = $this->id;
        $hashids = new Hashids(Setting::HASH_SALT, 5);
        $confirm_id = $hashids->encode($id);
        
        return $confirm_id;
    }
}
