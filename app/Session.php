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
     * Convert day of week to Carbon const
     */
//    const DAY_OF_WEEK = [
//        'on_mondays'    => Carbon::MONDAY,
//        'on_tuesdays'   => Carbon::TUESDAY,
//        'on_wednesdays' => Carbon::WEDNESDAY,
//        'on_thursdays'  => Carbon::THURSDAY,
//        'on_fridays'    => Carbon::THURSDAY,
//        'on_saturdays'  => Carbon::FRIDAY,
//        'on_sundays'    => Carbon::SATURDAY
//    ];

    /**
     * Convert Carbon day const to session day
     */
    const DAY_OF_WEEK = [
        Carbon::MONDAY    => 'on_mondays',
        Carbon::TUESDAY   => 'on_tuesdays',
        Carbon::WEDNESDAY => 'on_wednesdays',
        Carbon::THURSDAY  => 'on_thursdays',
        Carbon::THURSDAY  => 'on_fridays',
        Carbon::FRIDAY    => 'on_saturdays',
        Carbon::SATURDAY  => 'on_sundays'
    ];

    protected  $table = 'session';
    
    public function isSpecial(){
        return $this->one_off == self::SPECIAL_SESSION;
    }



    /**
     * Relationship with Timing
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timings(){
        return $this->hasMany(Timing::class, 'session_id', 'id');
    }

    public function scopeNormalSession($query){
        return $query->where('one_off', self::NORMAL_SESSION)->with('timings');
    }

    public function scopeSpecialSession($query){
        $today = Carbon::now(Setting::TIME_ZONE);

        $query_max_day = Setting::where([
            'outlet_id' =>  1,
            'setting_group' => 'BUFFERS',
            'setting_key' => 'MAX_DAYS_IN_ADVANCE'
        ])->first();

        $max_days_in_advance = !is_null($query_max_day) ? $query_max_day : Setting::MAX_DAYS_IN_ADVANCE;


        $max_day = $today->copy()->addDays($max_days_in_advance);


        return $query->where([
            ['one_off', '=', self::SPECIAL_SESSION],
            ['one_off_date', '>=', $today->format('Y-m-d')],
            ['one_off_date',  '<', $max_day->format('Y-m-d')]
        ])->with('timings');
    }

    public function scopeAvailableSession($query){
        return $query->normalSession()
            ->orWhere(function($q){$q->specialSession();})
            ->with('timings');
    }

    protected function buildStep3(){
        $s = Session::availableSession()->get();
        $s1 = $s->map->assignDate()->collapse();

//        return $s1;
        $grouped  = $s1->groupBy(function($s){return $s->date->format('Y-m-d');});

        $grouped1 = $grouped->map(function($group){
            $collecttimings = collect([]);

            $group->each(function($session) use($collecttimings){

                //modify on timing before loose track
                /** @var Session $session */
                $session->assignInfoToTiming();
                $collecttimings->push($session->timings);
            });

            return $collecttimings->collapse();
        });
        
        $grouped2 = $grouped1->map(function($group){
            
            $chunk = $group->map(function($timing){
                /** @var Timing $timing */
                $chunk = $timing->chunkByInterval();
                return $chunk;
            })->collapse();


            $chunk1 = $chunk->sortBy(function($option, $index){
                //13:00:00 to 13 as int
                $timeInfo = explode(":", $option->time);
                $hour = (int) $timeInfo[0];
                $minute = (int) $timeInfo[1];


                return $hour * 60 + $minute;
            })->values();

            //walk compare to filter out
            $chunk2 = $chunk1->reduce(function($carry, $item){
                $push_new = true;
                //but when pop out
                $alreday_has = $carry->filter(function($t)use($item){return $t->time == $item->time;})->count() > 0;

                if($alreday_has){
                    $push_new = false;
                }


                //override case
                if($alreday_has && $item->type == 1){
                    $carry->pop();
                    $push_new = true;
                }

                //push
                if($push_new){
                    $carry->push($item);
                }

                return $carry;
            }, collect([]));

            $chunk3 = $chunk2->reduce(function($carry, $item){
                $pre_item = $carry->last();

                if(is_null($pre_item)){
                    $carry->push($item);
                    return $carry;
                }

                $delta = abs(Session::getMinutes($pre_item->time) - Session::getMinutes($item->time));

                $current_interval = $pre_item->interval_minutes;

                $condition1 = false;
                if($delta >= $current_interval){
//                    $carry->push($item);
                    $condition1 = true;
                }

                $condition2 = false;
//                ($pre_item->type == 0 && $item->type == 1)
                $first_arrival_time = $item->first_arrival_time;
                //respect first arrival time
                $respect_first_delta = abs(Session::getMinutes($item->time) - Session::getMinutes($first_arrival_time));

                if($respect_first_delta % $item->interval_minutes == 0){
                    $condition2 = true;
                }

                //change type
                $condition3 = false;
                if(($pre_item->type == 0 && $item->type == 1) && $delta < $current_interval){
                    $carry->pop();
                    $condition3 = true;
                }

                $push_new = $condition1 && $condition2 || $condition3;
                if($push_new){
                    $carry->push($item);
                }
                return $carry;
            }, collect([]));

//            var_dump($chunk2);

            return $chunk3;
        });


        return $grouped2;
    }

    public function assignInfoToTiming(){
        $type = $this->one_off;
        $this->timings->each(function($t) use($type){
            $t->type = $type;
        });
    }
    
    public function availableOnDay($session_day){
        return $this->$session_day == self::DAY_AVAILABLE;
    }

    public function assignDate(){
        $session_collection = collect([]);
        if($this->isSpecial()){
            $this->date = Carbon::createFromFormat('Y-m-d', $this->one_off_date, Setting::TIME_ZONE);

            return $session_collection->push($this);
        }

        if(!$this->isSpecial()){
            $today = Carbon::now(Setting::TIME_ZONE);
            foreach(self::DAY_OF_WEEK as $carbon_day => $session_day){
                if($this->availableOnDay($session_day)){
                    $as = clone $this;
                    $as->date = $today->copy()->addDays($carbon_day -  $today->dayOfWeek);

                    $session_collection->push($as);
                }
            }

        }

        return $session_collection;
    }

}
