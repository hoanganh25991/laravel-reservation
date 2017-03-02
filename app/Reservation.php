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
    
    protected function valid(){
        $valid_reservations   = Reservation::validInDateRange()->get();
        dd($valid_reservations);
        $c = $valid_reservations->sum('pax_size');
        dd($c);
        $reservations_by_date = $valid_reservations->groupBy(function($r){return $r->date->format('Y-m-d');});
        $reservations_by_time =
            $reservations_by_date->map(function($group){
                return $group->groupBy(function($r){
                    return $r->date->format('H:i:s');
                })->map->sum('pax_size');
            });

        dd($reservations_by_time);
        
        return $reservations_by_time;
    }
    
    public function getDateAttribute(){
        return $this->reservation_timestamp;
    }

    public function getPaxSize(){
        return ($this->adult_pax + $this->children_pax);
    }

    public function getReservationTimestampAttribute($date_tring){
        return Carbon::createFromFormat('Y-m-d H:i:s', $date_tring, Setting::TIME_ZONE);
    }
}
