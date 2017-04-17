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
        return $this->max_pax ?: Setting::TIMING_MAX_PAX;
    }

    /**
     * __get magic call to wrap case where property not set
     * Instead of throw exception, no property > return null
     * Magicall for $this->abc as getAbcAttribute
     * @param $field
     * @return mixed|null
     */
    public function __get($field){
        $method   = 'get'.Str::studly($field).'Attribute';
        if(method_exists($this, $method))
            return $this->$method();

        // If property not found
        // Return as null
        try {
            return $this->$field;
        }catch(\Exception $e){
            return null;
        }
    }

    public function __set($field, $value){
        $method   = 'set'.Str::studly($field).'Attribute';
        if(method_exists($this, $method))
            return $this->$method($value);

        $this->$field = $value;
    }
}
