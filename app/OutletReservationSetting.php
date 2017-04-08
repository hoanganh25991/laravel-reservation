<?php

namespace App;

use Carbon\Carbon;
use App\Traits\ApiUtils;
use App\Libraries\HoiHash;
use Illuminate\Support\Collection;
use App\OutletReservationSetting as Setting;

/**
 * @property mixed $outlet_id
 * @property mixed $setting_key
 * @property mixed $setting_value
 * @see App\OutletReservationSetting::outletId
 */
class OutletReservationSetting extends HoiModel{

    use ApiUtils;

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

    //deposit value used for both fixed & per pax
    const DEPOSIT_VALUE = 'DEPOSIT_VALUE';
    const DEFAULT_DEPOSIT_VALUE = 5; //$5
    
    /**
     * Overral pax
     * Min pax
     * Max pax
     * To validate select pax form
     */
    const OVERALL_MIN_PAX = 'OVERALL_MIN_PAX';
    const DEFAULT_OVERALL_MIN_PAX = 2;

    const OVERALL_MAX_PAX = 'OVERALL_MAX_PAX';
    const DEFAULT_OVERALL_MAX_PAX = 20;

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
    const TIMING_MAX_PAX = 20;

    /**
     * Hash id SALT
     */
    const HASH_SALT = 'Hashids is a small open-source library that generates short, unique, non-sequential ids from numbers.';

    protected $table = 'res_outlet_reservation_setting';

    /**
     * Protect model from unwanted column when build query
     */
    protected $guarded = ['id'];

    protected $fillable = [
        'outlet_id',
        'setting_group',
        'setting_key',
        'setting_value',
        'setting_type',
    ];

    /**
     * Store outlet config
     */
    public static $all_config = null;

    public static $brand_id   = null;

    public static $outlet_id  = null;

    /**
     * Inject into boot process
     * To modify on query scope or
     * Listen eloquent event : creating, saving, updating,...
     */
    protected static function boot(){
        parent::boot();

        static::creating(function(Setting $setting){
            if(!isset($setting->attributes['outlet_id'])){
                $setting->outlet_id = Setting::outletId();
            }
        });

        /**
         * Right before save into database
         * Setting key cross check if it allowed
         * Discard running save it validate fail
         */
        static::saving(function(Setting $setting){
            $setting_key = $setting->setting_key;
            $allowed     = in_array($setting_key, Setting::allowedChangeSettingKeys());

            if(!$allowed){
                return false;
            }

            return true;
        });

        static::byOutletId();
    }

    /**
     * Acceptably changeable keys
     * @return array
     */
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
            //for deposit
            Setting::REQUIRE_DEPOSIT,
            Setting::DEPOSIT_THRESHOLD_PAX,
            Setting::DEPOSIT_TYPE,
            Setting::DEPOSIT_VALUE,
            //for settings
            //Setting::BRAND_ID,
            Setting::SMS_SENDER_NAME,
            Setting::OVERALL_MIN_PAX,
            Setting::OVERALL_MAX_PAX,
        ];
    }

    /**
     * Convenience way to find or create new one
     * $condistion is where clause into database condition
     * @param array $condition
     * @return Setting $config
     */
    public static function findOrNew($condition){
        $config = Setting::where($condition)->first();
        
        if(is_null($config)){
            $config = new Setting($condition);
        }
        
        return $config;
    }

    /**
     * Timezone set up for this reservation server
     * @return string
     */
    public static function timezone(){
        //return config('app.timezone');
        return env('TIMEZONE', 'Asia/Singapore');
    }

    /**
     * Get all config for an outlet by group
     * Build convenience function call style as map
     * Store in static to reuse in current request
     * @return mixed
     */
    public static function allConfigByGroup(){
        if(!is_null(Setting::$all_config)){
            return Setting::$all_config;
        }

        $setting = new Setting;
        $config  = $setting->query()->get();
        //config by group
        $config_by_group =
            $config
                ->groupBy(function ($c){ return $c->setting_group; })
                ->map(function ($group) use ($setting){
                    return $setting->buildConfigAsMap($group);
                });

        //Store all config to reuse in this request
        Setting::$all_config = $config_by_group;

        return Setting::$all_config;
    }

    /**
     * Available date range for booking
     * Base on
     *      +current time
     *      +buffer_config  MAX_DAYS_IN_ADVANCE
     *
     * @return array
     */
    public static function dateRange(){
        $buffer_config       = Setting::bufferConfig();
        $max_days_in_advance = $buffer_config(Setting::MAX_DAYS_IN_ADVANCE);

        $today   = Carbon::now(Setting::timezone());
        $max_day = $today->copy()->addDays($max_days_in_advance);

        return [$today, $max_day];
    }

    /**
     * Set up global query scope "outlet_id"
     * @param $outlet_id
     */
    public static function injectOutletId($outlet_id){
        Setting::$outlet_id = $outlet_id;
    }

    /**
     * Get global query scope "outlet_id"
     * @return mixed
     * @throws \Exception
     */
    public static function outletId(){
        if(is_null(Setting::$outlet_id)){
            throw new \Exception('Please tell me which outlet_id used!');
        }

        return Setting::$outlet_id;
    }

    /**
     * Config of each group
     * @param int $group_name
     * @return \Closure
     */
    public static function getConfigGroup($group_name = Setting::BUFFER_GROUP){
        $config_by_group = Setting::allConfigByGroup();

        try{
            $config_group = $config_by_group[(string)$group_name];
        }catch(\Exception $e){
            //build default as empty map
            return (new Setting)->buildConfigAsMap(collect([]));
        }

        return $config_group;
    }

    /**
     * Alias of allConfigByGroup for specific group
     * @return \Closure
     */
    public static function bufferConfig(){
        return Setting::getConfigGroup(Setting::BUFFER_GROUP);
    }

    public static function settingsConfig(){
        return Setting::getConfigGroup(Setting::SETTINGS_GROUP);
    }

    public static function notificationConfig(){
        return Setting::getConfigGroup(Setting::NOTIFICATION_GROUP);
    }

    public static function depositConfig(){
        return Setting::getConfigGroup(Setting::DEPOSIT_GROUP);
    }


    /**
     * Inject "brand_id"
     * for global scope query
     * @param $brand_id
     */
    public static function injectBrandId($brand_id){
        Setting::$brand_id = $brand_id;
    }

    /**
     * Get "brand_id"
     * Only handlde outlets under this brand
     */
    public static function brandId(){
        if(is_null(Setting::$brand_id)){
            throw new \Exception('Please tell me which brand_id used!');
        }

        return Setting::$brand_id;
    }

    /**
     * Sender name for SMS
     * @return string|null
     */
    public static function smsSenderName(){
        $setting_config = Setting::settingsConfig();

        return $setting_config(Setting::SMS_SENDER_NAME);
    }

    /**
     * Config stored in closure function
     * ex:
     *      to get MAX_DAYS_IN_ADVANCE
     *      call $buffer_config('MAX_DAYS_IN_ADVANCE');
     * 
     * @param $group
     * @return \Closure
     */
    private function buildConfigAsMap($group){
        $group->getKey = function ($key){
            /* @var Collection $this */
            //Find item has key
            $item = $this->filter(function ($i) use ($key){ return $i->setting_key == $key; })->first();

            //No item found, use default config
            if(is_null($item)){
                try{
                    $setting_class = new \ReflectionClass(Setting::class);
                    $item_value    = $setting_class->getConstant("DEFAULT_$key");

                    return $item_value;
                }catch(\Exception $e){
                    $msg = "Key [$key] : not found in database & doesn't have default config";
                    throw new \Exception($msg);
                }
            }

            //normally, get value in item
            $item_value = $item->setting_value;

            //check $item_value type
            switch($item->setting_type){
                case Setting::STRING:
                    $item_value = (string)$item_value;
                    break;
                case Setting::INT:
                    $item_value = (int)$item_value;
                    break;
            }

            return $item_value;
        };
        
        //return as closure function
        return $group->getKey->bindTo($group);
    }

    /**
     * Wrap create Hash, to decode|encode id
     * @return HoiHash
     */
    public static function hash(){
        $hash = new HoiHash();

        return $hash;
    }

    /**
     * Input array of keys, output map of key => value
     * @param \Closure $config
     * @param array $keys
     * @return array
     */
    public static function buildKeyValueOfConfig($config, $keys = []){
        $map = [];
        
        foreach($keys as $key){
            $map[$key] = $config($key);
        }

        return $map;
    }
}
