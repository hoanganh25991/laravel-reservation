<?php

namespace App;

use App\Traits\ApiUtils;
use Illuminate\Database\Eloquent\Model;
use App\OutletReservationSetting as Setting;

class OutletReservationSetting extends Model {

    use ApiUtils;

    const TIME_ZONE = 'Asia/Singapore';

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

    protected $table = 'outlet_reservation_setting';
    
    
    public function scopeBufferConfig($query){
        return $query->where('setting_group', self::BUFFER_GROUP);
    }

    /**
     * Dynamic function call getKey on buffer
     * @return string
     */
    protected function getBufferConfig(){
        //$config = static::bufferConfig()->get();
        $config = Setting::bufferConfig()->get();
        /**
         * Train config how to getKey
         * Dynamic add up function to object
         * ~ prototype in js, JUST nearly like
         */
        $config->getKey = $this->buildGetKey();
        
        return $config->getKey->bindTo($config);
    }






}
