<?php

namespace App;
use Illuminate\Database\Eloquent\Builder;
use App\OutletReservationSetting as Setting;
/**
 * @property mixed outlet_name
 */
class Outlet extends HoiModel {
    
    protected $table = 'outlet';
    
    public function getNameAttribute(){
        return $this->outlet_name;
    }

    protected static function boot() {
        parent::boot();

        static::byBrandId();
    }
    
    protected static function byBrandId(){
        static::addGlobalScope('brand_id', function(Builder $buidler){
            $brand_id = Setting::brandId();
            $buidler->where('brand_id', $brand_id);
        });
    }
}
