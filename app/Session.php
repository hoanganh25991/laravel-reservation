<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\OutletReservationSetting as Setting;

class Session extends Model
{
    //when one_off = 0
    //mean that this session use for MANY DAYS
    //not just ONE DAY
    //NORMAL_SESSION
    const NORMAL_SESSION = 0;
    const DAY_AVAILABLE  = 1;

    const SPECIAL_SESSION = 1;

    const DAY_OF_WEEK = [
        'on_mondays'    => Carbon::MONDAY,
        'on_tuesdays'   => Carbon::TUESDAY,
        'on_wednesdays' => Carbon::WEDNESDAY,
        'on_thursdays'  => Carbon::THURSDAY,
        'on_fridays'    => Carbon::THURSDAY,
        'on_saturdays'  => Carbon::FRIDAY,
        'on_sundays'    => Carbon::SATURDAY
    ];

    protected  $table = 'session';

//    public function isAvailableOnDay(Carbon $day){
//        if($this->one_off == self::NORMAL_SESSION){
//            collect(self::DAY_OF_WEEK)->each
//        }
//    }

    protected function availableV2(){
        $s = Session::availableSession()->get();
        
        return $s;
    }
    
    public function scopeAvailableSession($query){
        $today = Carbon::now(Setting::TIME_ZONE);

        $query_max_day = Setting::where([
            'outlet_id' =>  1,
            'setting_group' => 'BUFFERS',
            'setting_key' => 'MAX_DAYS_IN_ADVANCE'
        ])->first();

        $max_days_in_advance = !is_null($query_max_day) ? $query_max_day : Setting::MAX_DAYS_IN_ADVANCE;


        $max_day = $today->copy()->addDays($max_days_in_advance);
        
        
        return $query->where('one_off', self::NORMAL_SESSION)
                     ->orWhere([
                        ['one_off', '=', self::SPECIAL_SESSION],
                        ['one_off_date', '>=', $today->format('Y-m-d')],
                        ['one_off_date',  '<', $max_day->format('Y-m-d')]
                     ])
                     ->with('timings');
    }

    protected function available(){
        $a = collect([]);

        $a = $a->merge(Session::normalSession()->get());
        $a = $a->merge(Session::specialSession()->get());

        return $a;
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

    public function timings(){
        return $this->hasMany(Timing::class, 'session_id', 'id');
    }
    
    protected function buildStep1(){
        $s = $this->available();

        $today = Carbon::now(Setting::TIME_ZONE);
        $start_day = $today->copy();
        $max = 7;
        $end_day = $today->copy()->addDays($max);

//        $available_days = [];
//
//        $count = 0;
//        while($start_day->day + count <= $end_day->day){
//            //session theo ngay
//
//
//
//
//
//            $count++;
//        }
        $days_collection = collect([]);
//        var_dump($s);
        $s->each(function($session) use($days_collection){
            if($session->one_off == self::NORMAL_SESSION){
                $today = Carbon::now(Setting::TIME_ZONE);
                $today_day_of_week = $today->dayOfWeek;
                //find out $monday
                //$monday = $today->copy()->addDays(Carbon::MONDAY - $today_day_of_week);
                foreach(self::DAY_OF_WEEK as $session_day => $carbon_value){
                    if($session[$session_day] == self::DAY_AVAILABLE){
                        $as = clone $session;
                        $as->date = $today->copy()->addDays($carbon_value - $today_day_of_week);

                        $days_collection->push($as);
                    }
                }
            }

            if($session->one_off == self::SPECIAL_SESSION && $session->one_off_date != NULL){
                $session->date = Carbon::createFromFormat('Y-m-d', $session->one_off_date)->timezone(Setting::TIME_ZONE);
                $days_collection->push($session);
            }
        });

        $grouped  = $days_collection->groupBy(function($s){return $s->date->format('Y-m-d');});

//        $aaa = collect([]);
//        $aaa = $grouped->map(function($group, $groupName) use($aaa){
        $aaa = $grouped->map(function($group) {
            $timeline = new NodeTimeline();
            $group->each(function($session) use($timeline){
                $session->timings->each(function($t) use($session,$timeline){
                    //$t->chunkByInterval();
//                    $t->session = $session;
                    $timeline->push($t, $session->one_off);
                });
            });

//            $aaa->push([$groupName => $timeline->getNodeArray()]);
            return $timeline->getNodeArray();
        });

//        return $grouped;
        $ccc = $aaa->map(function($group){
            $a = $this->extractTimeSlot($group);
            return $a;
        });

        return $ccc;
    }


    public function extractTimeSlot($group){
        $a = collect([]);

        $group->reduce(function($carry, $item) use($a){
            if(empty($carry)){
                return $item;
            }
            $first = $carry->time;
            $last = $item->time;

            $first_time = Carbon::createFromFormat('H:i:s', $first, Setting::TIME_ZONE);
            $last_time = Carbon::createFromFormat('H:i:s', $last, Setting::TIME_ZONE);

            $info = $carry->first_info;
            $info = ($item->type == 1 && !is_null($item->last_info)) ? $item->last_info : $info;

            if(empty($info)){
                $info = $carry->last_info;
            }

            if(empty($info)){
                echo "FUCK";
                return;
            }

            while($first_time->lt($last_time)){
                $option = [
                    'time' => $first_time->format('H:i'),
                    'capacity_1' => $info->capacity_1,
                    'capacity_2' => $info->capacity_2,
                    'capacity_3_4' => $info->capacity_3_4,
                    'capacity_5_6' => $info->capacity_5_6,
                    'capacity_7_x' => $info->capacity_7_x
                ];
                $a->push($option);
                $first_time->addMinutes($info->interval_minutes);
            }

            return $item;
        });

        //dd($a);

        return $a;
    }

    protected function buildStep2(){
        $today = Carbon::now(Setting::TIME_ZONE);
        
        $start_day = $today->copy();
        
        $max = 7;
        
        $end_day = $today->copy()->addDays($max);
        
        $available_days = collect([]);
        
        $index = $start_day->copy();
        while($index->lte($end_day)){
            $available_days->push($index->copy());
            $index->addDays(1);
        }

        $s = $this->available();
        $days_collection = collect([]);
//        var_dump($s);
        $s->each(function($session) use($days_collection){
            if($session->one_off == self::NORMAL_SESSION){
                $today = Carbon::now(Setting::TIME_ZONE);
                $today_day_of_week = $today->dayOfWeek;
                //find out $monday
                //$monday = $today->copy()->addDays(Carbon::MONDAY - $today_day_of_week);
                foreach(self::DAY_OF_WEEK as $session_day => $carbon_value){
                    if($session[$session_day] == self::ASSIGNED){
                        $as = clone $session;
                        $as->date = $today->copy()->addDays($carbon_value - $today_day_of_week);

                        $days_collection->push($as);
                    }
                }
            }

            if($session->one_off == self::SPECIAL_SESSION && $session->one_off_date != NULL){
                $session->date = Carbon::createFromFormat('Y-m-d', $session->one_off_date)->timezone(Setting::TIME_ZONE);
                $days_collection->push($session);
            }
        });

        $grouped  = $days_collection->groupBy(function($s){return $s->date->format('Y-m-d');});


        $a = $grouped->map(function($group){
            $specail = $group->filter(function($s){return $s->one_off == 1;});
            $normal  = $group->filter(function($s){return $s->one_off == 0;});

            $n_timings = $normal->map(function($s){return $s->timings;})->collapse();
            $s_timings = $specail->map(function($s){return $s->timings;})->collapse();

            $a = $n_timings->map(function($t){
                $t->chunkByInterval();
                return $t->chunk;
            })->collapse()->collapse();

            $b = $s_timings->map(function($t){
                $t->chunkByInterval();
                return $t->chunk;
            })->collapse()->collapse();
//
            $c = $a->merge($b);
            return $c;
//            return $b;
        });

        return $a;
    }

    protected function buildStep3(){
        $s = Session::availableV2();
        $s1 = $s->map(function($session){
            return $session->withDate();
        })->collapse();

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
                //but when pop out
                if($item->type == 1){
                    $alreday_has = $carry->filter(function($t)use($item){return $t->time == $item->time;})->count();
                    if($alreday_has > 0){
                        $carry->pop(); //lay ra ko xai
                    }
                }

                //allway push
                $carry->push($item);

                return $carry;
            }, collect([]));

            return $chunk2;
        });


        return $grouped2;
    }

    public function assignInfoToTiming(){
        $type = $this->one_off;
        $this->timings->each(function($t) use($type){
            $t->type = $type;
        });
    }

    public function withDate(){
        $c = collect([]);
        if($this->one_off == self::NORMAL_SESSION){
            $today = Carbon::now(Setting::TIME_ZONE);
            $today_day_of_week = $today->dayOfWeek;
            //find out $monday
            //$monday = $today->copy()->addDays(Carbon::MONDAY - $today_day_of_week);
            foreach(self::DAY_OF_WEEK as $session_day => $carbon_value){
                if($this[$session_day] == self::DAY_AVAILABLE){
                    $as = clone $this;
                    $as->date = $today->copy()->addDays($carbon_value - $today_day_of_week);

                    $c->push($as);
                }
            }
        }




        if($this->one_off == self::SPECIAL_SESSION && $this->one_off_date != NULL){
            $this->date = Carbon::createFromFormat('Y-m-d', $this->one_off_date)->timezone(Setting::TIME_ZONE);

            return $c->push($this);
        }

        //
        return $c;
    }

}
