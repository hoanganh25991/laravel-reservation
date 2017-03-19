<?php

namespace App;

use Carbon\Carbon;
use App\Traits\ApiUtils;
use App\Libraries\HoiHash;
use App\OutletReservationSetting as Setting;
use Illuminate\Database\Eloquent\Collection;

class OutletReservationSetting extends HoiModel{

    use ApiUtils;

    //const TIME_ZONE = 'Asia/Singapore';

    /**
     * BUFFER default config
     */
    const BUFFER_GROUP = 0;

    const MAX_DAYS_IN_ADVANCE = 'MAX_DAYS_IN_ADVANCE';
    const DEFAULT_MAX_DAYS_IN_ADVANCE = 7;

    const MIN_HOURS_IN_ADVANCE_SLOT_TIME = 'MIN_HOURS_IN_ADVANCE_SLOT_TIME';
    const DEFAULT_MIN_HOURS_IN_ADVANCE_SLOT_TIME = 3;

    const MIN_HOURS_IN_ADVANCE_SESSION_TIME = 'MIN_HOURS_IN_ADVANCE_SESSION_TIME';
    const DEFAULT_MIN_HOURS_IN_ADVANCE_SESSION_TIME = 3;

    /**
     * SETTING default config
     */
    const SETTING_GROUP = 1;

    const BRAND_ID = 'BRAND_ID';
    const DEFAULT_BRAND_ID = 1;

    const SMS_SENDER_NAME = 'SMS_SENDER_NAME';
    const DEFAULT_SMS_SENDER_NAME = 'ALFRED';

    /**
     * NOTIFICATION default config
     */
    const NOTIFICATION_GROUP = 2;
    const SHOULD_SEND = 1;

    const SEND_SMS_ON_BOOKING = 'SEND_SMS_ON_BOOKING';
    const DEFAULT_SEND_SMS_ON_BOOKING = 1;

    const HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM = 'HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM';
    const DEFAULT_HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM = 2;

    const SEND_SMS_TO_CONFIRM_RESERVATION = 'SEND_SMS_TO_CONFIRM_RESERVATION';
    const DEFAULT_SEND_SMS_TO_CONFIRM_RESERVATION = 1;

    /**
     * Cast value by type
     */
    const STRING = 0;
    const INT = 1;

    /**
     * Default fallback when pax size not set
     */
    const RESERVATION_PAX_SIZE = 'RESERVATION_PAX_SIZE';
    const DEFAULT_RESERVATION_PAX_SIZE = 7;

    /**
     * Default fallback when max pax not set
     */
    const TIMING_MAX_PAX = 10;

    /**
     * Hash id SALT
     */
    const HASH_SALT = 'Hashids is a small open-source library that generates short, unique, non-sequential ids from numbers.';

    protected $table = 'outlet_reservation_setting';

    public static $all_config = null;

    protected static function boot(){
        parent::boot();

        static::byOutletId();
    }

    public static function timezone(){
        //return config('app.timezone');
        return env('TIMEZONE', 'Asia/Singapore');
    }

    public static function bufferConfig(){
        return (new Setting)->getConfigGroup(Setting::BUFFER_GROUP);
    }

    private static function allConfigByGroup(){
        $setting = new Setting;

        if(!is_null(Setting::$all_config)){
            return Setting::$all_config;
        }

        $config = $setting->query()->get();
        //config by group
        $config_by_group =
            $config->groupBy(function ($c){ return $c->setting_group; })->map(function ($group) use ($setting){
                return $setting->buildConfigAsMap($group);
            });

        //Store all config to reuse in this request
        Setting::$all_config = $config_by_group;

        return Setting::$all_config;
    }

    private function getConfigGroup($group_name = Setting::BUFFER_GROUP){
        $config_by_group = Setting::allConfigByGroup();

        try{
            $config_group = $config_by_group[(string)$group_name];
        }catch(\Exception $e){
            return (new Setting)->buildConfigAsMap(collect([]));
        }

        return $config_group;
    }

    public static function dateRange(){
        $buffer_config = Setting::bufferConfig();
        $max_days_in_advance = $buffer_config(Setting::MAX_DAYS_IN_ADVANCE);

        $today = Carbon::now(Setting::timezone());
        $max_day = $today->copy()->addDays($max_days_in_advance);

        return [
            $today,
            $max_day
        ];
    }

    public static function outletId(){
        return session('outlet_id', 1);
    }

    public static function brandId(){
        $setting_config = Setting::settingConfig();

        return $setting_config('BRAND_ID');
    }

    public static function smsSenderName(){
        $setting_config = Setting::settingConfig();

        return $setting_config(Setting::SMS_SENDER_NAME);
    }

    public static function settingConfig(){
        return (new Setting)->getConfigGroup(Setting::SETTING_GROUP);
    }

    public static function notificationConfig(){
        return (new Setting)->getConfigGroup(Setting::NOTIFICATION_GROUP);
    }

    /**
     * as a map, ex: $setting_config
     * can get 'HOURS_BEFORE_RESERVATION_TIME_TO_SEND_SMS
     * by $setting_config('HOURS_BEFORE_RESERVATION_TIME_TO_SEND_SMS');
     * @param $group
     * @return \Closure
     */
    private function buildConfigAsMap($group){
        $group->getKey = function ($key){
            /* @var Collection $this */
            $item = $this->filter(function ($i) use ($key){ return $i->setting_key == $key; })->first();

            /**
             * When no item found, use default config
             */
            if(is_null($item)){
                try{
                    $setting_class = new \ReflectionClass(Setting::class);
                    $item_value = $setting_class->getConstant("DEFAULT_$key");
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
                    $item_value = (int)$item_value;
                    break;
            }

            /**
             * Special key config
             */
            switch($key){
//                case 'HOURS_BEFORE_RESERVATION_TIME_TO_SEND_SMS':
//                    if(env('APP_ENV') != 'production'){
//                        $item_value = 0;
//                    }
//                    break;

            }

            return $item_value;
        };

        return $group->getKey->bindTo($group);
    }

    public static function hash(){
        $hash = new HoiHash();

        return $hash;
    }
}
