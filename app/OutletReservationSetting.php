<?php

namespace App;

use Carbon\Carbon;
use App\Traits\ApiUtils;
use App\Libraries\HoiHash;
use App\OutletReservationSetting as Setting;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property mixed outlet_id
 * @see App\OutletReservationSetting::outletId
 */
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
    const SETTINGS_GROUP = 1;

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

    const SEND_SMS_CONFIRMATION = 'SEND_SMS_CONFIRMATION';
    const DEFAULT_SEND_SMS_TO_CONFIRM_RESERVATION = 1;

    /**
     * DEPOSIT GROUP
     */
    const DEPOSIT_GROUP = 3;
    const REQUIRED = 1;

    const DEPOSIT_THRESHOLD_PAX = 'DEPOSIT_THRESHOLD_PAX';
    const DEFAULT_DEPOSIT_THRESHOLD_PAX = 30;

    const REQUIRE_DEPOSIT = 'REQUIRE_DEPOSIT';
    const DEFAULT_REQUIRE_DEPOSIT = 1;

    const DEPOSIT_TYPE = 'DEPOSIT_TYPE';
    const DEFAULT_DEPOSIT_TYPE = 0;
    const FIXED_SUM = 0;
    const PER_PAX   = 1;

    /**
     * Use deposit value both for fixed & per pax
     */
    const DEPOSIT_VALUE = 'DEPOSIT_VALUE';
    const DEFAULT_DEPOSIT_VALUE = 5; //$5
    
//    const FIXED_SUM_VALUE = 'FIXED_SUM_VALUE';
//    const DEFAULT_FIXED_SUM_VALUE = 5;
//
//    const PER_PAX_VALUE = 'PER_PAX_VALUE';
//    const DEFAULT_PER_PAX_VALUE = 5;

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

    protected $guarded = ['id'];

    public static $all_config = null;

    protected static function boot(){
        parent::boot();

        static::creating(function(Setting $setting){
            if(!isset($setting->attributes['outlet_id'])){
                $setting->outlet_id = Setting::outletId();
            }
        });

        static::byOutletId();
    }

    public static function allowedChangeSettingKeys(){
        return [
            //for buffer
            Setting::MAX_DAYS_IN_ADVANCE,
            Setting::MIN_HOURS_IN_ADVANCE_SLOT_TIME,
            Setting::MIN_HOURS_IN_ADVANCE_SESSION_TIME,
            //for notification
            Setting::SEND_SMS_ON_BOOKING,
            Setting::SEND_SMS_CONFIRMATION,
            Setting::HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM,
            Setting::BRAND_ID,
            Setting::SMS_SENDER_NAME,
            //for deposit
            Setting::REQUIRE_DEPOSIT,
            Setting::DEPOSIT_THRESHOLD_PAX,
            Setting::DEPOSIT_TYPE,
            Setting::DEPOSIT_VALUE,
        ];
    }

    public static function timezone(){
        //return config('app.timezone');
        return env('TIMEZONE', 'Asia/Singapore');
    }

    /**
     * Get all config for an outlet
     * Group in each grooup
     * Build convenience function call style as map
     * Store in static to reuse in current request
     * @return mixed
     */
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

    /**
     * Available date range for booking
     * Base on current time & buffer config of
     * MAX_DAYS_IN_ADVANCE
     * @return array
     */
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

    /**
     * Session, Reservation filter by outlet id
     * outlet_id as global scope when query in database
     * @return mixed
     */
    public static function outletId(){
        return session('outlet_id', 1);
    }

    /**
     * Config of each group
     * @param int $group_name
     * @return \Closure
     */
    private function getConfigGroup($group_name = Setting::BUFFER_GROUP){
        $config_by_group = Setting::allConfigByGroup();

        try{
            $config_group = $config_by_group[(string)$group_name];
        }catch(\Exception $e){
            return (new Setting)->buildConfigAsMap(collect([]));
        }

        return $config_group;
    }

    /**
     * Alias of allConfigByGroup for specific group
     * @see OutletReservationSetting::getConfigGroup
     * convenience call
     * @return \Closure
     */
    public static function bufferConfig(){
        return (new Setting)->getConfigGroup(Setting::BUFFER_GROUP);
    }

    public static function settingsConfig(){
        return (new Setting)->getConfigGroup(Setting::SETTINGS_GROUP);
    }

    public static function notificationConfig(){
        return (new Setting)->getConfigGroup(Setting::NOTIFICATION_GROUP);
    }

    public static function depositConfig(){
        return (new Setting)->getConfigGroup(Setting::DEPOSIT_GROUP);
    }

    /**
     * Base on config
     * Convenience call
     */
    /**
     * Oulet filter by specific brand id in config
     * @return mixed
     */
    public static function brandId(){
        $setting_config = Setting::settingsConfig();

        return $setting_config(Setting::BRAND_ID);
    }

    /**
     * Send SMS with sender name
     * @return mixed
     */
    public static function smsSenderName(){
        $setting_config = Setting::settingsConfig();

        return $setting_config(Setting::SMS_SENDER_NAME);
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

    /**
     * Wrap create Hash on id
     * @return HoiHash
     */
    public static function hash(){
        $hash = new HoiHash();

        return $hash;
    }

    /**
     * Build app-state for config
     * as key => value on specific config
     * @param \Closure $config
     * @param array $keys
     */
    public static function buildKeyValueOfConfig($config, $keys = []){
        $collect_keys = collect($keys);

        /** @var TYPE_NAME $collect_keys */
        return $collect_keys->map(/**
         * @param $key
         * @return mixed
         */
            function($key) use($config){
            return [$key => $config($key)];
        })->collapse();
    }
}
