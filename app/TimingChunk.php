<?php

namespace App;

use App\OutletReservationSetting as Setting;

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

    public function getMaxPaxAttribute(){
        return $this->max_pax ?: Setting::TIMING_MAX_PAX;
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
