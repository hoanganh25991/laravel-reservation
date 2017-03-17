<?php

namespace App;

use Carbon\Carbon;
use App\Traits\ApiUtils;
use App\OutletReservationSetting as Setting;
use Illuminate\Database\Eloquent\Collection;

class OutletReservationSetting extends HoiModel {

    use ApiUtils;

    //const TIME_ZONE = 'Asia/Singapore';

    /**
     * BUFFER default config
     */
    const BUFFER_GROUP                      = 0;
    const MAX_DAYS_IN_ADVANCE               = 7;
    const MIN_HOURS_IN_ADVANCE_SLOT_TIME    = 3;
    const MIN_HOURS_IN_ADVANCE_SESSION_TIME = 3;

    /**
     * SETTING default config
     */
    const SETTING_GROUP   = 1;
    const BRAND_ID        = 1;
    const SMS_SENDER_NAME = 'ALFRED';

    /**
     * Cast value by type
     */
    const STRING = 0;
    const INT    = 1;

    /**
     * Default fallback when pax size not set
     */
    const RESERVATION_PAX_SIZE = 7;

    /**
     * Default fallback when max pax not set
     */
    const TIMING_MAX_PAX = 8;

    /**
     * Hash id SALT
     */
    const HASH_SALT = 'Hashids is a small open-source library that generates short, unique, non-sequential ids from numbers.';

    protected $table = 'outlet_reservation_setting';

    public static $buffer_config = null;
    /** @var Collection $all_config */
    public static $all_config = null;

    protected static function boot(){
        parent::boot();

        static::byOutletId();
    }

    public static function timezone(){
        //return config('app.timezone');
        return env('TIMEZONE', 'Asia/Singapore');
    }

    public function scopeBufferConfig($query){
        return $query->where('setting_group', Setting::BUFFER_GROUP);
    }

    /**
     * Dynamic function call getKey on buffer
     * @return string
     */
    protected function bufferConfigAsMap(){
        //$config = static::bufferConfig()->get();
        $config = Setting::$buffer_config ?: $this->scopeBufferConfig($this->query())->get();
        //assign to static for reuse ONLY in this request
        Setting::$buffer_config = $config;

        /**
         * Train config how to getKey
         * Dynamic add up function to object
         * ~ prototype in js, JUST nearly like
         */
        $config->getKey = $this->buildGetKey();
        
        return $config->getKey->bindTo($config);
    }

    /**
     * @return Carbon[]
     */
    protected function dateRange(){
        $buffer_config = Setting::bufferConfigAsMap();
        $max_days_in_advance = $buffer_config('MAX_DAYS_IN_ADVANCE');

        $today   = Carbon::now(Setting::timezone());
        $max_day = $today->copy()->addDays($max_days_in_advance);

        return [$today, $max_day];
    }
    
    public static function outletId(){
        return session('outlet_id', 1);
    }
    
    public function scopeAllConfig(){
        //query database to get data
        $config = Setting::$all_config ?: Setting::all();
        //dd($config);
        $config_by_group = 
            $config
                ->groupBy(function($c){return $c->setting_group;})
                ->map(function($group){
                    $group->getKey = $this->buildGetKey();
                    
                    return $group->getKey->bindTo($group);
                });
        
        return $config_by_group;
    }
    
    public static function brandId(){
        $config = Setting::allConfig();
        //dd($config);
        try{
            $setting_config = $config[(string)Setting::SETTING_GROUP];
        }catch(\Exception $e){
            return Setting::BRAND_ID;
        }

        return $setting_config('BRAND_ID') ?: Setting::BRAND_ID;
    }

    public static function smsSenderName(){
        $config = Setting::allConfig();
        //dd($config);
        try{
            $setting_config = $config[(string)Setting::SETTING_GROUP];
        }catch(\Exception $e){
            return Setting::BRAND_ID;
        }

        return $setting_config('SMS_SENDER_NAME') ?: Setting::SMS_SENDER_NAME;
    }
}
