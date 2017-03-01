<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OutletReservationSetting extends Model {

    const TIME_ZONE = 'Asia/Singapore';

    /**
     * BUFFER default config
     */
    const MAX_DAYS_IN_ADVANCE               = 7;
    const MIN_HOURS_IN_ADVANCE_SLOT_TIME    = 3;
    const MIN_HOURS_IN_ADVANCE_SESSION_TIME = 3;

    protected $table = 'outlet_reservation_setting';
    
    
    
    
    
}
