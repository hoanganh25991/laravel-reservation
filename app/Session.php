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
    const SPECIAL_SESSION = 1;
    const ASSIGN_DAY_OF_WEEK = [
        'on_mondays' => Carbon::MONDAY,
        'on_tuesdays' => Carbon::TUESDAY,
        'on_wednesdays' => Carbon::WEDNESDAY,
        'on_thursdays' => Carbon::THURSDAY,
        'on_fridays' => Carbon::THURSDAY,
        'on_saturdays' => Carbon::FRIDAY,
        'on_sundays' => Carbon::SATURDAY
    ];
    const ASSIGNED = 1;

    protected  $table = 'session';



//    public static function available(){
//        $a = collect([]);
//
//        $a = $a->merge(Session::normalSession()->get());
//        $a = $a->merge(Session::specialSession()->get());
//
//        return $a;
//    }

//    public function available(){
    protected function available(){
        $a = collect([]);

        $a = $a->merge(Session::normalSession()->get());
        $a = $a->merge(Session::specialSession()->get());

        return $a;
    }

    public function scopeNormalSession($query){
        return $query->where('one_off', self::NORMAL_SESSION);
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
        ]);
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
                foreach(self::ASSIGN_DAY_OF_WEEK as $session_day => $carbon_value){
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

        $grouped->each(function($group, $groupName){
//            var_dump($group);
//            var_dump($groupName);
//            die;
            $group->each(function($session){
                $session->timings->each(function($t){
                    $t->chunkByInterval();
                });
            });
        });

        return $grouped;
    }

}
