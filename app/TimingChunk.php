<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use App\OutletReservationSetting as Setting;

class TimingChunk implements Arrayable, Jsonable{
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

    protected $fillable = [
        'time'               ,
        'session_type'       ,
        'session_name'       ,
        'first_arrival_time' ,
        'interval_minutes'   ,
        'capacity_1'         ,
        'capacity_2'         ,
        'capacity_3_4'       ,
        'capacity_5_6'       ,
        'capacity_7_x'       ,
        'max_pax'            ,
        'children_allowed'   ,
    ];

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

    /**
     * Build json
     * @param int $options
     * @return string
     */
    public function toJson($options = 0){
        return json_encode($this->toArray(), JSON_NUMERIC_CHECK);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(){
        $arr = [];
        foreach($this->fillable as $key){
            $arr[$key] = $this->$key;
        }

        return $arr;
    }
}
