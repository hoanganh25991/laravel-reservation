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
 * @property int   $setting_group
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

    const MIN_HOURS_IN_ADVANCE_TO_ALLOW_CANCELLATION_AMENDMENT_PRIOR_TO_RESERVATION_TIME = 
        'MIN_HOURS_IN_ADVANCE_TO_ALLOW_CANCELLATION_AMENDMENT_PRIOR_TO_RESERVATION_TIME';

    const MAX_PAX_FOR_SELF_CANCELLATION_AMENDMENT = 'MAX_PAX_FOR_SELF_CANCELLATION_AMENDMENT';
    const DEFAULT_MAX_PAX_FOR_SELF_CANCELLATION_AMENDMENT = 16;

    /**
     * SETTING default config
     */
    const SETTINGS_GROUP = 1;

    const BRAND_ID = 'BRAND_ID';
    const DEFAULT_BRAND_ID = 1;

    const SMS_SENDER_NAME = 'SMS_SENDER_NAME';
    const DEFAULT_SMS_SENDER_NAME = 'ALFRED';

    // Overral pax | Min pax | Max pax
    // To validate select pax form
    const OVERALL_MIN_PAX = 'OVERALL_MIN_PAX';
    const DEFAULT_OVERALL_MIN_PAX = 2;

    /**
     * NOTIFICATION default config
     */
    const NOTIFICATION_GROUP = 2;
    const SHOULD_SEND = 1;

    const SEND_SMS_ON_BOOKING = 'SEND_SMS_ON_BOOKING';
    const DEFAULT_SEND_SMS_ON_BOOKING = 1;
    
    const SEND_EMAIL_ON_BOOKING = 'SEND_EMAIL_ON_BOOKING';
    const DEFAULT_SEND_EMAIL_ON_BOOKING = 0;

    const HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM = 'HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM';
    const DEFAULT_HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM = 2;

    const SEND_SMS_CONFIRMATION = 'SEND_SMS_CONFIRMATION';
    const DEFAULT_SEND_SMS_CONFIRMATION = 1;

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

    const PAYPAL_TOKEN = 'PAYPAL_TOKEN';
    // no default value for PAYPAL_TOKEN

    const PAYPAL_CURRENCY = 'PAYPAL_CURRENCY';
    const DEFAULT_PAYPAL_CURRENCY = 'SGD';
    
    const SUPPORTED_PAYPAL_CURRENCY = 'SUPPORTED_PAYPAL_CURRENCY';
    const DEFAULT_SUPPORTED_PAYPAL_CURRENCY = [
        'USD' => 'US Dollar',
        'SGD' => 'Singapore Dollar',
        'MYR' => 'Malaysian Ringgit',
    ];




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
    const DEFAULT_TIMING_MAX_TABLE_SIZE = 20;

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
            $allowed     = Setting::allowedChangeSettingKeys()->contains($setting_key);

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
        $keys = [
            //for buffer
            Setting::MAX_DAYS_IN_ADVANCE,
            Setting::MIN_HOURS_IN_ADVANCE_SLOT_TIME,
            Setting::MIN_HOURS_IN_ADVANCE_SESSION_TIME,
            Setting::MIN_HOURS_IN_ADVANCE_TO_ALLOW_CANCELLATION_AMENDMENT_PRIOR_TO_RESERVATION_TIME,
            Setting::MAX_PAX_FOR_SELF_CANCELLATION_AMENDMENT,
            //for notification
            Setting::SEND_SMS_ON_BOOKING,
            Setting::SEND_EMAIL_ON_BOOKING,
            Setting::SEND_SMS_CONFIRMATION,
            Setting::HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM,
            //for deposit
            Setting::REQUIRE_DEPOSIT,
            Setting::DEPOSIT_THRESHOLD_PAX,
            Setting::DEPOSIT_TYPE,
            Setting::DEPOSIT_VALUE,
            Setting::PAYPAL_TOKEN,
            Setting::PAYPAL_CURRENCY,
            //for settings
            //Setting::BRAND_ID,
            Setting::SMS_SENDER_NAME,
            Setting::OVERALL_MIN_PAX,
            Setting::OVERALL_MAX_PAX,
        ];

        return collect($keys);
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
            $config    = new Setting();
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
     * @param null $which_outlet
     * @return mixed
     * @throws \Exception
     */
    public static function allConfigByGroup($which_outlet = null){
        // Reset all_config, when called by new guy
        if(Setting::$outlet_id != $which_outlet){
            Setting::$all_config = null;
        }

        if(!is_null(Setting::$all_config)){
            return Setting::$all_config;
        }
        
        if(!is_null($which_outlet)){
            Setting::injectOutletId($which_outlet);
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
     * @throws \Exception
     */
    public static function injectOutletId($outlet_id){
        if(!is_numeric($outlet_id)){
            throw new \Exception('outlet id to inject MUST BE A NUMBER');
        }
        //HAVE TO RESET all config when outlet_id changed;
        //Better if recheck, then update
        if(Setting::$outlet_id != $outlet_id){
            Setting::$all_config = null;
        }

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
    
    public static function isOutletIdSetup(){
        return !is_null(Setting::$outlet_id);
    }

    /**
     * Config of each group
     * @param int $group_name
     * @param null $which_outlet
     * @return \Closure
     */
    public static function getConfigGroup($group_name = Setting::BUFFER_GROUP, $which_outlet = null){
        $config_by_group = Setting::allConfigByGroup($which_outlet);

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
     * @param $which_outlet
     * @return \Closure
     */
    public static function bufferConfig($which_outlet = null){
        return Setting::getConfigGroup(Setting::BUFFER_GROUP, $which_outlet);
    }

    public static function settingsConfig($which_outlet = null){
        return Setting::getConfigGroup(Setting::SETTINGS_GROUP, $which_outlet);
    }

    public static function notificationConfig($which_outlet = null){
        return Setting::getConfigGroup(Setting::NOTIFICATION_GROUP, $which_outlet);
    }

    public static function depositConfig($which_outlet = null){
        return Setting::getConfigGroup(Setting::DEPOSIT_GROUP, $which_outlet);
    }


    /**
     * Inject "brand_id"
     * for global scope query
     * @param $brand_id
     * @throws \Exception
     */
    public static function injectBrandId($brand_id){
        if(!is_numeric($brand_id)){
            throw new \Exception('Brand id to inject MUST BE A NUMBER');    
        }
        
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
    
    public static function isBranIdSetup(){
        return !is_null(Setting::$brand_id);
    }

    /**
     * Sender name for SMS
     * @return string|null
     */
    public static function smsSenderName(){
        $setting_config = Setting::settingsConfig();

        return $setting_config(Setting::SMS_SENDER_NAME);
    }

    public static function getMinHoursInAdvanceAllowCancellation(){
        $value = null;
        // Ask database first
        $outletSettingAtKey = Setting::where('setting_key', Setting::MIN_HOURS_IN_ADVANCE_TO_ALLOW_CANCELLATION_AMENDMENT_PRIOR_TO_RESERVATION_TIME)
                                ->get()->first();
        if($outletSettingAtKey && !is_null($outletSettingAtKey->setting_value)){
            $value = $outletSettingAtKey->setting_value;
        }

        // Nothign setup, try to create one
        if(is_null($value)){
            // Self build, this key base on both min hours prior to reservation time & session time
            $buffer_config = Setting::bufferConfig();
            $value = max($buffer_config(Setting::MIN_HOURS_IN_ADVANCE_SLOT_TIME), $buffer_config(Setting::MIN_HOURS_IN_ADVANCE_SESSION_TIME));
        }

        return $value;
    }

    public static function validateMinHoursInAdvanceAllowCancellation($value){
        $buffer_config = Setting::bufferConfig();
        $is_respect_booking_time = $value >= max($buffer_config(Setting::MIN_HOURS_IN_ADVANCE_SLOT_TIME),
                                                $buffer_config(Setting::MIN_HOURS_IN_ADVANCE_SESSION_TIME));
        if($is_respect_booking_time){
            return true;
        }

        return false;
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
            // This key base on both min hours prior to reservation time & session time
            // So when get it first time, build by get/set
            if($key == Setting::MIN_HOURS_IN_ADVANCE_TO_ALLOW_CANCELLATION_AMENDMENT_PRIOR_TO_RESERVATION_TIME){
                $value = Setting::getMinHoursInAdvanceAllowCancellation();
                return $value;
            }
            /* @var Collection $this */
            //Find item has key
            $item = $this->filter(function ($i) use ($key){ return $i->setting_key == $key; })->first();

            //No item found, use default config
            if(is_null($item)){
                try{
//                    $setting_class = new \ReflectionClass(Setting::class);
//                    $item_value    = $setting_class->getConstant("DEFAULT_$key");
                    $setting_class = Setting::class;
                    $constant_str  = "$setting_class::DEFAULT_$key";
                    $item_value    = constant($constant_str);

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
     * Only use it for admin setting page
     * Where $key don't have value set as null
     * @param \Closure $config
     * @param array $keys
     * @return array
     */
    public static function buildKeyValueOfConfig($config, $keys = []){
        $map = [];
        
        foreach($keys as $key){
            // When config get $key
            // If key not exist, should throw exception
            // For admin setting page
            // Accept return as null
            try{
                $map[$key] = $config($key);
            }catch(\Exception $e){
                $map[$key] = null;
            }
        }

        return $map;
    }

    public static function getSupportedPaypalCurrency(){
        return Setting::DEFAULT_SUPPORTED_PAYPAL_CURRENCY;
    }

    /**
     * Check if customer allowed to edit reservation
     * @param Reservation $reservation
     * @return bool
     */
    public static function isCustomerAllowedToEditReservation($reservation){
        // Load config
        $buffer_config = Setting::bufferConfig();
        $min_hours_customer_allowed_edit = $buffer_config(Setting::MIN_HOURS_IN_ADVANCE_TO_ALLOW_CANCELLATION_AMENDMENT_PRIOR_TO_RESERVATION_TIME);
        $max_pax_allowed_edit            = $buffer_config(Setting::MAX_PAX_FOR_SELF_CANCELLATION_AMENDMENT);

        $now = Carbon::now(Setting::timezone());
        $reservation_time = $reservation->date;

        $diff_in_hours = $now->diffInHours($reservation_time, false);
        $still_not_pass= $now->lte($reservation_time);
        
        $respect_time  = $diff_in_hours >= $min_hours_customer_allowed_edit && $still_not_pass;
        $respect_pax   = $reservation->pax_size <= $max_pax_allowed_edit;
        
        return $respect_time && $respect_pax;
    }
}
