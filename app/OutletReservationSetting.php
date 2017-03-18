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
     * NOTIFICATION default config
     */
    const NOTIFICATION_GROUP = 2;
    const HOURS_BEFORE_RESERVATION_TIME_TO_SEND_SMS = 2;

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
        $config->getKey = $this->functionGetKey();
        
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
    
    public static function allConfigByGroup(){
        $setting = new Setting;
        if(!is_null(Setting::$all_config)){
            return Setting::$all_config;
        }
        
        $config = $setting->query()->get();
        //config by group
        $config_by_group = 
            $config
                ->groupBy(function($c){return $c->setting_group;})
                ->map(function($group) use($setting){
                    $group->getKey = $setting->functionGetKey();
                    
                    return $group->getKey->bindTo($group);
                });
        
        //Store all config to reuse in this request
        Setting::$all_config = $config_by_group;
        
        return Setting::$all_config;
    }
    
    public static function brandId(){
        $config = Setting::allConfigByGroup();
        //dd($config);
        try{
            $setting_config = $config[(string)Setting::SETTING_GROUP];
        }catch(\Exception $e){
            return Setting::BRAND_ID;
        }

        return $setting_config('BRAND_ID') ?: Setting::BRAND_ID;
    }

    public static function smsSenderName(){
        $config = Setting::allConfigByGroup();
        //dd($config);
        try{
            $setting_config = $config[(string)Setting::SETTING_GROUP];
        }catch(\Exception $e){
            return Setting::SMS_SENDER_NAME;
        }

        return $setting_config('SMS_SENDER_NAME') ?: Setting::SMS_SENDER_NAME;
    }

    public static function notificationConfig(){
        $config_by_group = Setting::allConfigByGroup();

        try{
            $notification_config = $config_by_group[(string)Setting::NOTIFICATION_GROUP];
        }catch(\Exception $e){
            return (new Setting)->buildConfigAsMap(collect([]));
        }

        return $notification_config;
    }

    /**
     * as a map, ex: $setting_config
     * can get 'HOURS_BEFORE_RESERVATION_TIME_TO_SEND_SMS
     * by $setting_config('HOURS_BEFORE_RESERVATION_TIME_TO_SEND_SMS');
     * @param $group
     * @return \Closure
     */
    public function buildConfigAsMap($group){
        $group->getKey = function($key){
            /* @var Collection $this */
            $item = $this->filter(function($i) use($key){return $i->setting_key == $key;})->first();

            /**
             * When no item found, use default config
             */
            if(is_null($item)){
                try{
                    $setting_class = new \ReflectionClass(Setting::class);
                    $item_value = $setting_class->getConstant($key);
                    return $item_value;
                }catch(\Exception $e){
                    $msg = "Key $key can find in database & default config";
                    throw new \Exception($msg);
                }
            }

            /**
             * $item_value has type
             * check type to return
             */
            $item_value = $item->setting_value;
            switch($item->setting_type){
                case Setting::INT:
                    $item_value = (int) $item_value;
                    break;
            }

            /**
             * Special key config
             */
            switch($key){
                case 'HOURS_BEFORE_RESERVATION_TIME_TO_SEND_SMS':
                    if(env('APP_ENV') != 'production'){
                        $item_value = 0;
                    }
                    break;

            }

            return $item_value;
        };
        
        return $group->getKey->bindTo($group);
    }
}
