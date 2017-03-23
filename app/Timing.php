<?php

namespace App;

use Carbon\Carbon;
use App\Libraries\GCD;
use App\Traits\ApiUtils;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\OutletReservationSetting as Setting;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @property mixed $first_arrival_time
 * @property mixed $interval_minutes
 * @property mixed $capacity_1
 * @property mixed $last_arrival_time
 * @property mixed $capacity_2
 * @property mixed $capacity_3_4
 * @property mixed $capacity_5_6
 * @property mixed $type
 * @property Session $session
 * @property mixed $capacity_7_x
 * @property mixed $max_pax
 * @property mixed $min_pax_for_booking_deposit
 * 
 * @property mixed $disabled
 * @see Timing::getDisabledAttribute
 * 
 * @property mixed $children_allowed
 * @see Timing::getChildrenAllowedAttribute
 */
class Timing extends HoiModel {
    
    use ApiUtils;

    /**
     * Interval minute for user pick time
     * must follow these value
     */
    const INTERVAL_MINUTE_STEPS = [15, 20, 30, 60];

    /**
     * First arrival time & last arrival time pick rule
     */
    const ARRIVAL_STEPS  = [15];

    /**
     * Capcaity prefix
     */
    const CAPACITY_PREFIX = 'capacity';
    const CAPACITY_X = [
        'capacity_1',
        'capacity_2',
        'capacity_3_4',
        'capacity_5_6',
        'capacity_7_x'
    ];
    
    /** 
     * Children Allowed
     */
    const CHILDREN_ALLOWED = 1;
    const CHILDREN_NOT_ALLOWED = 0;

    /**
     * Timing disabled
     */
    const AVAILABLE = 0;
    const DISABLED  = 1;

    /**
     * Store Booking type Deposit Or Not Or...
     */
    const NO_DEPOSIT  = 0;
    const HAS_DEPOSIT = 1;
    
    
    

    protected $table = 'timing';
    
    protected $guarded = ['id'];

    protected static function boot(){
        parent::boot();

        static::orderByFirstArrival();

        static::creating(function(Timing $timing){

        });
    }
    
    public static function orderByFirstArrival(){
        static::addGlobalScope('order_by_first_arrival', function(Builder $builder){
            $builder->orderBy('first_arrival_time', 'asc');
        });
    }

    /**
     * Relationship with session
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function session(){
        return $this->hasOne(Session::class, 'id', 'session_id');
    }

    /**
     * Timing decide how to chunk base on its arrival time
     * @return \Illuminate\Support\Collection
     */
    public function getChunkAttribute(){
        $allow_steps = array_merge(self::INTERVAL_MINUTE_STEPS, self::ARRIVAL_STEPS);

        $minimum_interval_to_match = GCD::find($allow_steps);

        $start_time = Carbon::createFromFormat('H:i:s', $this->first_arrival_time);
        $end_time   = Carbon::createFromFormat('H:i:s', $this->last_arrival_time);

        $chunks = collect([]);

        while($start_time->lt($end_time)){

            $chunk = (object)[
                'time'               => $start_time->format('H:i'),
                'session_type'       => $this->session->type,
                'session_name'       => $this->session->session_name,
                'first_arrival_time' => $this->first_arrival_time,
                'interval_minutes'   => $this->interval_minutes,
                'capacity_1'         => $this->capacity_1,
                'capacity_2'         => $this->capacity_2,
                'capacity_3_4'       => $this->capacity_3_4,
                'capacity_5_6'       => $this->capacity_5_6,
                'capacity_7_x'       => $this->capacity_7_x,
                'max_pax'            => $this->max_pax,
                'children_allowed'   => $this->children_allowed,
            ];

            $chunks->push($chunk);

            $start_time->addMinutes($minimum_interval_to_match);
        }

        return $chunks;
    }

    /**
     * @param $pax_size
     * @return string
     */
    public static function getCapacityName($pax_size){
        $capacity_name = Timing::CAPACITY_PREFIX;

        $value = "1";
        
        switch($pax_size){
            case 1:
                $value = '1';
                break;
            case 2:
                $value = '2';
                break;
            case 3:
            case 4:
                $value = '3_4';
                break;
            case 5:
            case 6:
                $value = '5_6';
                break;
        }
        
        if($pax_size >= 7)
            $value = '7_x';
        
        return "{$capacity_name}_{$value}";
    }
    
    /**
     * Reuse Filter condition
     * Where clause on Builder to call into DATABASE   >>> FILTER
     * Has a collection than filter to find what match >>> FILTER
     * @param Collection|Builder|QueryBuilder|Relation $can_query
     * @return bool
     */
    public static function filterAvailableToBook($can_query){
        return $can_query->where('disabled', Timing::AVAILABLE);
    }

    /**
     * Timing which is available
     * @param $query
     * @return mixed
     */
    public function scopeAvailableToBook($query){
//        return $query->where('disabled', Timing::AVAILABLE);
        return Timing::filterAvailableToBook($query);
    }

    /**
     * Base on current config, timing store min pax for deposit rule
     * @param $val
     * @return bool
     */
    public function getChildrenAllowedAttribute($val){
        if(is_null($val)){
            return true;
        }
        
        return $val == Timing::CHILDREN_ALLOWED;
    }

    public function setChildrenAllowedAttribute($val){
        switch($val){
            case true:
            case "true":
                $sanity_val = Timing::CHILDREN_ALLOWED;
                break;
            case false:
            case "false":
                $sanity_val = Timing::CHILDREN_NOT_ALLOWED;
                break;
            default:
                $sanity_val = Timing::CHILDREN_ALLOWED;
                break;
        }
        
        $this->attributes['children_allowed'] = $sanity_val;
    }

    /**
     * Set/get on disabled attribute
     * Make sense when call as boolean
     */

    /**
     * When disabled state not set, default as available
     * @param $value
     * @return int
     */
    public function getDisabledAttribute($value){
        $value = is_null($value) ? Timing::AVAILABLE : $value;

        return $value == Timing::AVAILABLE;
    }

    /**
     * Convert boolean type in JSON
     * Client send Timing through JSON
     * When josn_decode, boolean as "true" | "false"
     * @param $value
     * @return int
     */
    public function setDisabledAttribute($value){
        switch($value){
            case true:
            case "true":
                $sanity_val = Timing::AVAILABLE;
                break;
            case false:
            case "false":
                $sanity_val = Timing::DISABLED;
                break;
            default:
                $sanity_val = Timing::AVAILABLE;
                break;
        }
        
        $this->attributes['disabled'] = $sanity_val;
    }
}
