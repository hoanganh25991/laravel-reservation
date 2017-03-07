<?php

namespace App;

use App\Traits\ApiUtils;
use Carbon\Carbon;
use Hamcrest\Core\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\OutletReservationSetting as Setting;

class OutletReservationSetting extends Model {

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
     * Cache filename
     */
    public static $buffer_config = null;

    protected $table = 'outlet_reservation_setting';

    protected function timezone(){
        //return config('app.timezone');
        return env('TIMEZONE');
    }

    public function scopeBufferConfig($query){
        return $query->where('setting_group', self::BUFFER_GROUP);
    }

    /**
     * Dynamic function call getKey on buffer
     * @return string
     */
    protected function bufferConfigAsMap(){
        //$config = static::bufferConfig()->get();
        $config = Setting::$buffer_config ?: Setting::bufferConfig()->get();
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


    protected static function boot() {
        parent::boot();

        $outlet_id = session('outlet_id');

        if(!is_null($outlet_id)){
            static::addGlobalScope('base on outlet', function (Builder $builder) use($outlet_id){
                $builder->where('outlet_id', $outlet_id);
            });
        }
    }



}
