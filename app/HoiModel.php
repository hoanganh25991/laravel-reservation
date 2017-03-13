<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
    
    protected static function byOutletId(){
        $outlet_id = session('outlet_id', 1);

        static::addGlobalScope('outlet_id', function (Builder $builder) use($outlet_id){
            $builder->where('outlet_id', $outlet_id);
        });
    }
}
