<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\OutletReservationSetting as Setting;

class HoiModel extends Model {
    
    /**
     * Name of the "created at" column.
     */
    const CREATED_AT = 'created_timestamp';

    /**
     * Name of the "updated at" column.
     */
    const UPDATED_AT = 'modified_timestamp';

    /**
     * Global scope query by outlet id
     */
    protected static function byOutletId(){
        static::addGlobalScope('outlet_id', function (Builder $builder){
            $outlet_id = Setting::outletId();
            $builder->where('outlet_id', $outlet_id);
        });
    }

    /**
     * Global scope query by brand id
     */
    public static function byBrandId(){
        static::addGlobalScope('brand_id', function(Builder $buidler){
            $brand_id = Setting::brandId();
            $buidler->where('brand_id', $brand_id);
        });
    }

    /**
     * Find out one by Id or create new on fail
     * Convenience way to insert/update bundle
     * @param $id
     * @return static
     */
    public static function findOrNew($id){
        $model = static::find($id);

        if(is_null($model)){
            $model = new static;
        }
        
        return $model;
    }
}
