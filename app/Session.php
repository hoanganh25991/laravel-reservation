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
    const SPECIAL_SESSIOn = 1;
    protected  $table = 'session';


    public static function available(){
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
            ['one_off', '=', self::SPECIAL_SESSIOn],
            ['one_off_date', '>=', $today->format('Y-m-d')],
            ['one_off_date',  '<', $max_day->format('Y-m-d')]
        ]);
    }
}
