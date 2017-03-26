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
     * @param $model_data
     * @return mixed
     */
    public static function sanityData($model_data){
        $model = new static;
        /**
         * Sanity through mutator
         */
//        $mutators = $model->getMutatedAttributes();
//
//        foreach($mutators as $mutator){
//            if(isset($model_data[$mutator])){
//                $model_data[$mutator] = $model->setAttribute($mutator, $model_data[$mutator]);
//            }
//        }

        /**
         * Sanity by limit column access
         */
        foreach($model->getArrayableAppends() as $append){
            unset($model_data[$append]);
        }

        //unset($model_data['id']);

        return $model_data;
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

    /**
     * Client submit JSON boolean
     * Transform into what data store as int
     * @param $val
     * @return int
     */
    public function getJsonBoolean($val){
        if(is_bool($val)){
            $val = $val ? 1 : 0;
        }

        return $val;
    }
}
