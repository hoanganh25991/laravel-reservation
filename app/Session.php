<?php

namespace App;

use Carbon\Carbon;
use App\Traits\ApiUtils;
//use App\Library\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\OutletReservationSetting as Setting;

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
     * @return \Illuminate\Support\Collection
     */
    public function assignDate(){
        $sessions = collect([]);

        if($this->isSpecial()){
            /* @case one_off_date NULL */  
            $this->date = Carbon::createFromFormat('Y-m-d', $this->one_off_date, Setting::TIME_ZONE);

            return $sessions->push($this);
        }

        if(!$this->isSpecial()){
            $today = Carbon::now(Setting::TIME_ZONE);
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
