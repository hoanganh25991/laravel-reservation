<?php

namespace App;

use App\OutletReservationSetting as Setting;
use Illuminate\Support\Str;

class TimingChunk {
    protected $time;
    protected $session_type;
    protected $first_arrival_time;
    protected $interval_minutes;
    protected $capacity_1;
    protected $capacity_2;
    protected $capacity_3_4;
    protected $capacity_5_6;
    protected $capacity_7_x;
    protected $max_pax;
    protected $children_allowed;
    /**
     * 
     */
    protected $min_pax_bookinf_for_deposit;
    protected $min_pax_for_booking_deposit;
    protected $booking_deposit_amount;

    public function __construct($attributes = []){
        foreach($attributes as $key => $value){
            $this->$key = $value;
        }
    }

    public function getMaxPaxAttribute(){
        $null_val = is_null($this->max_pax);
        return $null_val ? Setting::TIMING_MAX_PAX : $this->max_pax;
    }

    public function __get($field){
        $method   = 'get'.Str::studly($field).'Attribute';
        if(method_exists($this, $method))
            return $this->$method();

        return $this->$field;
    }

    public function __set($field, $value){
        $method   = 'set'.Str::studly($field).'Attribute';
        if(method_exists($this, $method))
            return $this->$method($value);

        $this->$field = $value;
    }
}
