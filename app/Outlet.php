<?php

namespace App;
use Illuminate\Database\Eloquent\Builder;
use App\OutletReservationSetting as Setting;

/**
 * @property mixed outlet_name
 * @property mixed id
 */
class Outlet extends HoiModel {
    
    protected $table = 'outlet';
    
    public function getNameAttribute(){
        return $this->outlet_name;
    }

    protected static function boot() {
        parent::boot();

        //static::byBrandId();
    }
    
    public static function validateHandledOutletId($value){
        $outlet_ids = Outlet::all()->pluck('id');
        
        return $outlet_ids->contains($value);
    }
    
}
