<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\OutletReservationSetting as Setting;

class HoiModel extends Model {
    
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_timestamp';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'modified_timestamp';

    public function scopeHasNewUpdate($query){
        $today = Carbon::now(Setting::timezone());
        //format as Y-m-d to implicit tell that H:i:s = 00:00:00
        $today_string = $today->format('Y-m-d');
        
        $filename = $this->getCacheFilenameLastUpdate();

        $last_update = Cache::get($filename, $today_string);
        //update info about last session update
        Cache::put($filename, $today->format('Y-m-d H:i:s'), 24 * 60);

        return $query
            //->where('created_timestamp', '>=', $last_sesion_update)
            ->orWhere('modified_timestamp', '>=', $last_update)
            ;
    }
    
    public function getCacheFilenameLastUpdate(){
        $class_name = strtoupper(get_class($this));
        
        return  "LAST_CHECK_ON_{$class_name}";
    }

    /**
     * Global scope query by outlet id
     * static call on this function to assign global scope on query
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
    protected static function byBrandId(){
        static::addGlobalScope('brand_id', function(Builder $buidler){
            $brand_id = Setting::brandId();
            $buidler->where('brand_id', $brand_id);
        });
    }


    /**
     * Rebuild model from array data (decode on JSON)
     * Need sanity data before run mutiple update
     * Which faster than parse into model & save
     * @param $timing_arr
     * @return mixed
     */
    public function sanityData($timing_arr){
        /**
         * Sanity through mutator
         */
        $mutators = $this->getMutatedAttributes();

        foreach($mutators as $mutator){
            if(isset($timing_arr[$mutator])){
                $timing_arr[$mutator] = $this->setAttribute($mutator, $timing_arr[$mutator]);
            }
        }

        /**
         * Sanity by limit column access
         */
        foreach($this->getArrayableAppends() as $append){
            if(isset($timing_arr[$append])){
                unset($timing_arr[$append]);
            }
        }

        return $timing_arr;
    }
}
