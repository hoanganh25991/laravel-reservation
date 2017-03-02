<?php

namespace App;

use App\Traits\ApiUtils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class OutletReservationSetting extends Model {

    use ApiUtils;

    const TIME_ZONE = 'Asia/Singapore';

    /**
     * BUFFER default config
     */
    const BUFFER_GROUP_NAME                 = 0;
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
        return $query->where('setting_group', self::BUFFER_GROUP_NAME);
    }

    /**
     * Dynamic function call getKey on buffer
     * @return string
     */
    protected function getBufferConfig(){
        $config = static::bufferConfig()->get();
        /**
         * Train config how to getKey
         */
        $config->getKey = $this->buildGetKey();
        
        return $config->getKey->bindTo($config);
    }






}
