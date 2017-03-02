<?php

namespace App;

use Carbon\Carbon;
use App\Traits\ApiUtils;
use App\OutletReservationSetting as Setting;

/**
 * @property mixed reservation_timestamp
 * @property static date
 * @property mixed adult_pax
 * @property mixed children_pax
 * @property mixed pax_size
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
}
