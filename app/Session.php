<?php

namespace App;

use Carbon\Carbon;
use App\Traits\ApiUtils;
//use App\Library\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\OutletReservationSetting as Setting;
use Symfony\Component\DependencyInjection\Tests\Compiler\C;

/**
 * @property mixed one_off
 * @property static date
 * @property mixed one_off_date
 * @property mixed timings
 * @property mixed type
 * @property mixed session_name
 */
class Session extends Model {
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
    const DAY_AVAILABLE  = 1;

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

    protected  $table = 'session';
    
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

    public function scopeNormalSession($query){
        return $query->where('one_off', Session::NORMAL_SESSION);
    }

    public function scopeSpecialSession($query){
       $date_range = $this->availableDateRange();

        return $query->where([
            ['one_off', '=', Session::SPECIAL_SESSION],
            ['one_off_date', '>=', $date_range[0]->format('Y-m-d')],
            ['one_off_date',  '<', $date_range[1]->format('Y-m-d')]
        ]);
    }

    public function scopeAvailableSession($query){
        return $query->normalSession()
            ->orWhere(function($q){$q->specialSession();})
            ->with('timings.session');
    }

    protected function availableTime(){
        $available_sessions = Session::availableSession()->get()->map->assignDate()->collapse();
        $sessions_by_date   = $available_sessions->groupBy(function($s){return $s->date->format('Y-m-d');});
        $timings_by_date    = $sessions_by_date->map(function($g){return $g->map->timings->collapse();});

        $date_with_available_time =
            $timings_by_date->map(function($group, $date_string){
                $chunks  = $group->map->chunk->collapse();

                $ordered_chunks = $chunks->sortBy(function($c){return $this->getMinutes($c->time);})->values();

                /**
                 * Special timing chunk will override on normal one
                 */
                $merged_chunks =
                    $ordered_chunks->reduce(function($carry, $item){
                        /**
                         *
                         */
                        $alreday_has = $carry->filter(function($last_item)use($item){return $last_item->time == $item->time;})->count() > 0;

                        /**
                         * overlap item is special, so override on pre_item
                         * bcs item sort out by order
                         * 2 item at same time > special item chose
                         */

                        $overlap_item_is_special = $alreday_has && $item->session_type == Session::SPECIAL_SESSION;

                        if($overlap_item_is_special)
                            $carry->pop();

                        /**
                         * Decide push item
                         */
                        $push_new = !$alreday_has || $overlap_item_is_special;

                        if($push_new)
                            $carry->push($item);


                        return $carry;
                    }, collect([]));


                $fixed_interval_chunks =
                    $merged_chunks->reduce(function($carry, $item){
                        /**
                         * First push item
                         */
                        $pre_item = $carry->last();
                        //should return immediately to prevent call on null
                        //of following step
                        if(is_null($pre_item)){
                            $carry->push($item);
                            return $carry;
                        }

                        /**
                         * satisfied interval > should push
                         */
                        $delta_time_with_pre = abs(Session::getMinutes($pre_item->time) - Session::getMinutes($item->time));
                        $satisfied_interval  = $delta_time_with_pre >= $pre_item->interval_minutes;

                        /**
                         * new item must pushed
                         */
                        $new_item_is_special_than_pre = ($pre_item->session_type == Session::NORMAL_SESSION
                                                        && $item->session_type == Session::SPECIAL_SESSION);
                        $new_item_must_pushed = $new_item_is_special_than_pre && !$satisfied_interval;
                        if($new_item_must_pushed)
                            $carry->pop();


                        /**
                         * Respect first arrival
                         */
                        $delta_time_with_first_arrival = abs(Session::getMinutes($item->time) - Session::getMinutes($item->first_arrival_time));
                        $respect_first_arrival = ($delta_time_with_first_arrival % $item->interval_minutes) == 0;

                        /**
                         * Check push new
                         */
                        $push_new = ($satisfied_interval || $new_item_must_pushed) && $respect_first_arrival;

                        if($push_new)
                            $carry->push($item);

                        return $carry;
                    }, collect([]));

                //BUFFER config check
                $buffer_config = Setting::getBufferConfig();
                /**
                 * Check minimum hours in advance for slot time
                 */
                /** @var string $buffer_config */
                $min_hours_slot_time    = $buffer_config('MIN_HOURS_IN_ADVANCE_SLOT_TIME');
                $min_hours_session_time = $buffer_config('MIN_HOURS_IN_ADVANCE_SESSION_TIME');

                $satisfied_prior_slot_time_chunks = $fixed_interval_chunks;
                $today = Carbon::now();
                $current_date =  Carbon::createFromFormat('Y-m-d', $date_string);
                if($current_date->diffInDays($today) < 1){
                    $satisfied_prior_slot_time_chunks =
                        $fixed_interval_chunks->filter(function($item) use($min_hours_slot_time, $min_hours_session_time){
                            $item_time_hours = $this->getMinutes($item->time) / 60;

                            $satisfied_in_advance_slot_time    = $item_time_hours >= $min_hours_slot_time;
                            $satisfied_in_advance_session_time = $item_time_hours >= $min_hours_session_time;

                            return $satisfied_in_advance_slot_time
                                   && $satisfied_in_advance_session_time;
                        });
                }


                return $satisfied_prior_slot_time_chunks;
            });


        $today_string = Carbon::now()->format('Y-m-d');

        if(isset($date_with_available_time[$today_string])){
            //modify directly on $date_with_available_time
            $available_sessions = $date_with_available_time[$today_string];

//            dd($available_sessions);

            $date_with_available_time[$today_string] = $available_sessions->filter(function($chunk){return false;});
        }

        //$date_with_available_time
        
        /**
         * Check minimum hours in advance for session time
         */
        //$date_with_available_time

        return $date_with_available_time;
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
     * @return \Illuminate\Support\Collection
     */
    public function assignDate(){
        $sessions = collect([]);

        if($this->isSpecial()){
            /* @case one_off_date NULL */  
            $this->date = Carbon::createFromFormat('Y-m-d', $this->one_off_date);

            return $sessions->push($this);
        }

        if(!$this->isSpecial()){
            $today = Carbon::now();
            foreach(Session::DAY_OF_WEEK as $carbon_day => $session_day){
                $diff_in_day =  ($carbon_day - $today->dayOfWeek);

                $available_to_book = $this->availableOnDay($session_day) && ($diff_in_day >= 0);

                if($available_to_book){
                    $as = clone $this;
                    $as->date = $today->copy()->addDays($carbon_day -  $today->dayOfWeek);

                    $sessions->push($as);
                }
            }

        }

        return $sessions;
    }
    
    
    
    

}
