<?php

namespace App;

use Carbon\Carbon;
use App\Traits\ApiUtils;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use App\OutletReservationSetting as Setting;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @property mixed one_off
 * @property Carbon date
 * @property mixed one_off_date
 * @property Collection timings
 * @property mixed type
 * @property mixed session_name
 * @property mixed id
 * 
 * @property mixed $first_arrival_time
 * @see App\Session::getFirstArrivalTimeAttribute
 * 
 * @property mixed $last_arrival_time
 * @see App\Session::getLastArrivalTimeAttribute
 *
 * @method static normalSession
 * @see App\Session::scopeNormalSession
 *
 * @method static specialSession
 * @see App\Session::scopeSpecialSession
 *
 * @method static allSpecialSession
 * @see App\Session::scopeAllSpecialSession
 */
class Session extends HoiModel{

    use ApiUtils;

    /**
     * Session type
     * base on one_off
     * one_off == 0 > normal session
     * one_off == 1 > special session
     */
    const NORMAL_SESSION  = 0;
    const SPECIAL_SESSION = 1;

    /**
     * Normal session reused on_x day
     * if it's value = 1
     */
    const DAY_AVAILABLE = 1;

    /**
     * Convert Carbon day const to session day
     */
    const DAY_OF_WEEK = [
        Carbon::MONDAY    => 'on_mondays',
        Carbon::TUESDAY   => 'on_tuesdays',
        Carbon::WEDNESDAY => 'on_wednesdays',
        Carbon::THURSDAY  => 'on_thursdays',
        Carbon::FRIDAY    => 'on_fridays',
        Carbon::SATURDAY  => 'on_saturdays',
        Carbon::SUNDAY    => 'on_sundays'
    ];

    protected $table = 'res_session';

    protected $guarded = ['id', 'timings'];

    /**
     * Protect model from unwanted column when build query
     */
    protected $fillable = [
        'outlet_id',
        'session_name',
        'on_mondays',
        'on_tuesdays',
        'on_wednesdays',
        'on_thursdays',
        'on_fridays',
        'on_saturdays',
        'on_sundays',
        'one_off',
        'one_off_date'
    ];

    /**
     * add fields when serialize model
     * base on set/get, these fields computed
     * @see App\Session::getFirstArrivalTimeAttribute
     */
    protected $appends = [
        'first_arrival_time',
        'last_arrival_time',
    ];
    
    protected $casts = [];

    /**
     * Inject into boot process
     * To modify on query scope or
     * Listen eloquent event : creating, saving, updating,...
     */
    protected static function boot(){
        parent::boot();

        static::byOutletId();
    }

    /**
     * Validate when create/add/update...
     * @param array $session_data
     * @return \Illuminate\Validation\Validator
     */
    public static function validateOnCRUD($session_data){
        $validator = Validator::make($session_data, [
            "outlet_id"    => 'required|numeric',
            "session_name" => 'required'
        ]);

        return $validator;
    }

    public function isSpecial(){
        return $this->one_off == Session::SPECIAL_SESSION;
    }

    /**
     * Alias for one_off
     * one_off considered as type of session
     *      +when one_off = 0, means session reused for serveral days   > NORMAL
     *      +when one_off = 1, means session ONLY used for specific day > SPECIAL
     * @return mixed
     */
    public function getTypeAttribute(){
        return $this->one_off;
    }

    /**
     * Relationship with Timing
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timings(){
        return $this->hasMany(Timing::class, 'session_id', 'id');
    }

    /**
     * Query normal session
     * @param $query
     * @return mixed
     */
    public function scopeNormalSession($query){
        return $query->where('one_off', Session::NORMAL_SESSION);
    }

    /**
     * Query special session in date range
     * @see App\OutletReservationSetting::dateRange
     * @param $query
     * @return mixed
     */
    public function scopeSpecialSession($query){
        $date_range = Setting::dateRange();

        return $query->where([
            ['one_off',       '=', Session::SPECIAL_SESSION],
            ['one_off_date', '>=', $date_range[0]->format('Y-m-d')],
            ['one_off_date', '<=', $date_range[1]->format('Y-m-d')]
        ]);
    }

    /**
     * Combine both normal session & special session
     * @warn At this step, we still don't know session is available
     *       Session must have its earliest time satisfied
     *       buffer config : MIN_HOURS_IN_ADVANCE_SESSION_TIME
     *       Which base on its timings
     * @param $query
     * @return mixed
     */
    public function scopeMayAvailableSession($query){
        return 
            $query
                ->normalSession()
                ->orWhere(function ($current_query){ $current_query->specialSession(); })
                ->with([
                    'timings' => function (Relation $relation){
                        /**
                         * Query on $relation overload to its Builder inside
                         * Timing when call query X, also overload on this Builder
                         * @see Builder
                         * @see Timing
                         */
                        /**
                         * Find timing which available
                         * @see Timing::scopeAvailableToBook
                         */
//                        return $relation->where('disabled', Timing::AVAILABLE)->with('session');
                        Timing::filterAvailableToBook($relation);
                    }
                ]);
    }

    /**
     * Check normal session available on day x of week
     * @param string $session_day
     * @return bool
     */
    public function availableOnDay($session_day){
        return $this->$session_day == Session::DAY_AVAILABLE;
    }



    /**
     * Assign date to session
     * @warn Normal session reused for serveral days
     *      return as collection of session with date
     *      to remain the consistent of return collection
     *      special session after assigned date
     *      also return a collection
     * @param null $date_range
     * @return Collection
     */
    public function assignDate($date_range = null){
        $sessions = collect([]);

        if($this->isSpecial()){
            //special session, without one_off_date specify
            //ignore it, no meaning
            if(is_null($this->one_off_date)){
                return $sessions;
            }

            $this->date = Carbon::createFromFormat('Y-m-d', $this->one_off_date, Setting::timezone());
            //store it
            return $sessions->push($this);
        }

        if(!$this->isSpecial()){
            $date_range = $date_range ?: Setting::dateRange();

            $current = $date_range[0]->copy();
            while($current->lte($date_range[1])){
                $current_day = $current->dayOfWeek;
                $session_day = Session::DAY_OF_WEEK[$current_day];

                if($this->availableOnDay($session_day)){
                    $as = clone $this;
                    $as->date = $current->copy();
                    //store it
                    $sessions->push($as);
                }

                //increase loop
                $current->addDay();

            }

        }

        return $sessions;
    }

    /**
     * Not available to book when
     * 1. No timings
     * AAA, session after filter out disabled timings
     * Has empty collection of timings
     * If this happen > not available to book
     *
     * 2. Min hours before session time
     * Bcs min hour before session time
     * Which turn session into unavailable to pick
     * Consider earliest time as session time
     * @see App\OutletReservationSetting::MIN_HOURS_IN_ADVANCE_SESSION_TIME
     *
     * @return bool
     */
    public function availableToBook(){
        //case 1 : No timings
        $is_no_timings = $this->timings->count() == 0;
        if($is_no_timings){
            return false;
        }

        //case 2 : Min hours before session time
        //Care on hours, only session different in time less than a day be checked
        $diff_less_than_a_day = Carbon::now(Setting::timezone())->diffInDays($this->date, false) == 0;
        if($diff_less_than_a_day){
            $earliest_timing          = $this->timings->first();
            $session_start_timing_str = $earliest_timing->first_arrival_time;

            $minutes     = $this->getMinutes($session_start_timing_str);
            $time_hour   = (int)round($minutes / 60);
            $time_minute = $minutes % 60;
            //compute exactly start timing of session
            $session_start_timing = $this->date->copy()->setTime($time_hour, $time_minute);

            $buffer_config = Setting::bufferConfig();
            $min_hours_session_time = $buffer_config(Setting::MIN_HOURS_IN_ADVANCE_SESSION_TIME);

            $diff_in_hours = Carbon::now(Setting::timezone())->diffInHours($session_start_timing, false);
            // Dif in hours >= min hours session time still not enough
            /** @case   min_hours = 0
             *          Select time just pass session time, less than 1 hours
             *          So, compare diffHours function still return as 0, exactly -0
             */
            $still_not_pass= Carbon::now(Setting::timezone())->lte($session_start_timing);
            // Allow pass through session start time as option of IGNORE prior to session start time
            // For this case min_hours_session_time will < 0, current set up is -1000
            $still_not_pass = ($min_hours_session_time < 0) || $still_not_pass;
            $satisfied_in_advance_session_time = $still_not_pass && $diff_in_hours >= $min_hours_session_time;

            return $satisfied_in_advance_session_time;
        }

        return true;
    }

    /**
     * Get all session which special
     * @see App\Session::scopeSpecialSession
     * @param $query
     * @return
     */
    public function scopeAllSpecialSession($query){
        return $query->where('one_off', Session::SPECIAL_SESSION);
    }

    /**
     * Session has timings, find the earliest first arrival time
     *
     * Bcs timings in session order by first arrival time by global query scope
     * Just get the first of first > earliest
     * @see App\Timing::orderByFirstArrival
     */
    public function getFirstArrivalTimeAttribute(){
        $timings = $this->timings;
        if($timings->isEmpty()){
            return null;
        }

        return $timings->first()->first_arrival_time;
    }

    /**
     * Session has timings, find the last of last arrival time
     */
    public function getLastArrivalTimeAttribute(){
        $timings = $this->timings;
        if($timings->isEmpty()){
            return null;
        }

        return $timings->last()->last_arrival_time;
    }


    /**
     * Override on serialization
     * @return array
     */
    public function attributesToArray() {
        $attributes = parent::attributesToArray();

        /**
         * After run assign date for specific date_range, session has date
         * Convert Carbon datetime obj to timestamp str
         * @see App\Session::assignDate
         */
        if(!is_null($this->date)){
            $attributes['date'] = $this->date->format('Y-m-d H:i:s');
        }
        
        return $attributes;
    }

    /**
     * Allow admin/reservations quick create a special session
     * as capacity 0 to close that time for reservation
     * @param $data
     * @return mixed
     */
    public static function validateCloseSlot($data){
        $allowed_outltes_id = Outlet::all()->pluck('id')->toArray();

        $validator = Validator::make($data, [
          'outlet_id'    => ['required', 'numeric', Rule::in($allowed_outltes_id)],
          'session_date' => 'required|date_format:Y-m-d',
        ]);

        return $validator;
    }
}
