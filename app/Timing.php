<?php

namespace App;

use Carbon\Carbon;
use App\Libraries\GCD;
use App\Traits\ApiUtils;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use App\OutletReservationSetting as Setting;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @property mixed $session_id
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
 * @property mixed $disabled
 * @property mixed $children_allowed
 */
class Timing extends HoiModel {
    
    use ApiUtils;

    /**
     * Interval minute for user pick time
     * Must follow these value
     */
    const INTERVAL_MINUTE_STEPS = [15, 30, 60];

    /**
     * First arrival time & last arrival time pick steps
     */
    const ARRIVAL_STEPS  = [30];

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
    
    protected $table = 'res_timing';
    
    protected $guarded = ['id'];

    protected $fillable = [
        'session_id',
        'timing_name',
        'disabled',
        'first_arrival_time',
        'last_arrival_time',
        'interval_minutes',
        'capacity_1',
        'capacity_2',
        'capacity_3_4',
        'capacity_5_6',
        'capacity_7_x',
        'max_pax',
        'children_allowed',
        'is_outdoor',
    ];

    protected $casts = [
        'disabled'         => 'boolean',
        'children_allowed' => 'boolean'
    ];

    protected static function boot(){
        parent::boot();

        static::orderByFirstArrival();

        static::creating(function(Timing $timing){

        });
    }

    /**
     * Validate first|last arrival time follow step
     * @see App\Timing::ARRIVAL_STEPS
     * @param $value
     * @return bool
     */
    public static function validateArrivalTime($value){
        $time = Carbon::createFromFormat('H:i:s', $value, Setting::timezone());
        $minute = $time->minute;

        $count = 0;
        $respect_step = false;
        while($count < count(Timing::ARRIVAL_STEPS) && !$respect_step){
            $step = Timing::ARRIVAL_STEPS[$count];

            if($minute % $step == 0){
                $respect_step = true;
            }

            $count++;
        }

        return $respect_step;
    }

    /**
     * Validate when create/add/update...
     * @param array $timing_data
     * @return \Illuminate\Validation\Validator
     */
    public static function validateOnCRUD($timing_data){
        $validator = Validator::make($timing_data, [
            //"session_id"         => 'required|numeric',
            "timing_name"        => 'required',
            "disabled"           => 'required|boolean',
            "first_arrival_time" => 'bail|required|date_format:H:i:s|arrival_time',
            "last_arrival_time"  => 'bail|required|date_format:H:i:s|arrival_time',
            "interval_minutes"   => ['required', Rule::in(Timing::INTERVAL_MINUTE_STEPS)],
            "capacity_1"         => 'required|numeric',
            "capacity_2"         => 'required|numeric',
            "capacity_3_4"       => 'required|numeric',
            "capacity_5_6"       => 'required|numeric',
            "capacity_7_x"       => 'required|numeric',
            "max_pax"            => 'required|numeric',
            "children_allowed"   => 'required|boolean',
        ]);

        return $validator;
    }

    /**
     * Global query scope, order timing by first arrival
     */
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
        //lt: less than
        //lte: less than & equal
        //in this case, "chunk" only record the first arrival time
        //use lt not lte to compare
        while($start_time->lt($end_time)){
            //store timing info in chunk
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
            //increase loop
            $start_time->addMinutes($minimum_interval_to_match);
        }

        return $chunks;
    }

    /**
     * Capacity name in column field base on pax size
     * @param $pax_size
     * @return string
     */
    public static function getCapacityName($pax_size){
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
            default:
                $value = '1';
                break;
        }
        
        if($pax_size >= 7)
            $value = '7_x';
        
        return "capacity_{$value}";
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
        return Timing::filterAvailableToBook($query);
    }
}
