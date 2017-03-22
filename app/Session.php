<?php

namespace App;

use Carbon\Carbon;
use App\Traits\ApiUtils;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\OutletReservationSetting as Setting;

/**
 * @property mixed one_off
 * @property Carbon date
 * @property mixed one_off_date
 * @property Collection timings
 * @property mixed type
 * @property mixed session_name
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
    const NORMAL_SESSION = 0;
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
        Carbon::MONDAY => 'on_mondays',
        Carbon::TUESDAY => 'on_tuesdays',
        Carbon::WEDNESDAY => 'on_wednesdays',
        Carbon::THURSDAY => 'on_thursdays',
        Carbon::FRIDAY => 'on_fridays',
        Carbon::SATURDAY => 'on_saturdays',
        Carbon::SUNDAY => 'on_sundays'
    ];

    protected $table = 'session';

    /**
     * Bring these computed field when serialize to JSON
     * @var array
     */
    protected $appends = [
        'first_arrival_time',
        'last_arrival_time'
    ];
    
    protected $casts = [];
    

    protected static function boot(){
        parent::boot();

        static::byOutletId();
    }

    public function isSpecial(){
        return $this->one_off == Session::SPECIAL_SESSION;
    }

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
     * @param $query
     * @return mixed
     */
    public function scopeSpecialSession($query){
        $date_range = Setting::dateRange();

        return $query->where([
            [
                'one_off',
                '=',
                Session::SPECIAL_SESSION
            ],
            [
                'one_off_date',
                '>=',
                $date_range[0]->format('Y-m-d')
            ],
            [
                'one_off_date',
                '<',
                $date_range[1]->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Combine both normal session & special session
     * @warn At this step, we still don't know session is available
     *           Session should have its earliest time satisfied MIN_HOURS_IN_ADVANCE_SESSION_TIME
     *       Which base on its Timing
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
     * @warn normal session can reuse for many days
     * > return as collection of session with date
     * > to normalize the consistent
     * > special session after assigned also return a collection
     * @param null $date_range
     * @return Collection
     */
    public function assignDate($date_range = null){
        $sessions = collect([]);

        if($this->isSpecial()){
            /* @case one_off_date NULL */
            $this->date = Carbon::createFromFormat('Y-m-d', $this->one_off_date, Setting::timezone());

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
     * @see Setting::MIN_HOURS_IN_ADVANCE_SESSION_TIME
     *
     * @return bool
     */
    public function availableToBook(){
        /**
         * No timings
         */
        $is_no_timings = $this->timings->count() == 0;
        if($is_no_timings){
            return false;
        }

        /**
         * Min hours before session time
         */
        $diff_less_than_a_day = Carbon::now(Setting::timezone())->diffInDays($this->date, false) == 0;
        /**
         * Care on hours, only check for
         * session different in time less than a day should be checked
         */
        if($diff_less_than_a_day){
            //dd($this->timings->first());
            $earliest_timing = $this->timings->first();
            // $is_no_timings guardrantee $earliest_timing not NULL
            $session_start_timing_str = $earliest_timing->first_arrival_time;
            $minutes = $this->getMinutes($session_start_timing_str);
            $time_hour = (int)round($minutes / 60);
            $time_minute = $minutes % 60;
            //compute exactly start timing of session
            $session_start_timing = $this->date->copy()->setTime($time_hour, $time_minute);

            $buffer_config = Setting::bufferConfig();
            $min_hours_session_time = $buffer_config(Setting::MIN_HOURS_IN_ADVANCE_SESSION_TIME);
            $diff_in_hours = Carbon::now(Setting::timezone())->diffInHours($session_start_timing, false);

            $satisfied_in_advance_session_time = $diff_in_hours > $min_hours_session_time;
            return $satisfied_in_advance_session_time;
        }

        return true;
    }

    /**
     * Get all session which special
     *
     * Other method
     * @see Session::scopeSpecialSession
     * Limit session in available date range
     * @param $query
     * @return
     */
    public function scopeAllSpecialSession($query){
        return $query->where('one_off', Session::SPECIAL_SESSION);
    }

    /**
     * Session has timings, find the earliest arrival time
     * Timings in session order by first arrival time
     * as global scope
     * @see Timing::orderByFirstArrival
     */
    public function getFirstArrivalTimeAttribute(){
        $timings = $this->timings;
        if($timings->isEmpty()){
            return null;
        }

        return $timings->first()->first_arrival_time;
    }

    /**
     * Session has timings, find the last arrival time
     */
    public function getLastArrivalTimeAttribute(){
        $timings = $this->timings;
        if($timings->isEmpty()){
            return null;
        }

        return $timings->last()->last_arrival_time;
    }
}
