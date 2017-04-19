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

    //protected $fillable = [];

    protected $appends = [
        'overall_min_pax',
        'overall_max_pax',
        'max_days_in_advance'
    ];
    
    public function getNameAttribute(){
        return $this->outlet_name;
    }

    protected static function boot() {
        parent::boot();

        static::byBrandId();
    }
    
    public static function validateHandledOutletId($value){
        $outlet_ids = Outlet::all()->pluck('id');
        
        return $outlet_ids->contains($value);
    }

    /**
     * Bring some outlet config to model
     * Which help frontend with better experience on user booking form
     */
    /**
     * Get overall_min_pax
     * For better experience with select pax form
     * @return mixed|null
     */
    public function getOverallMinPaxAttribute(){
        $outlet_id = $this->id;

        if(is_null($outlet_id)){
            // Outlet still not created
            // Can get its config
            return null;
        }

        $setting_config = Setting::settingsConfig($outlet_id);

        return $setting_config(Setting::OVERALL_MIN_PAX);
    }
    /**
     * Get overall_max_pax
     * For better experience with select pax form
     * @return mixed|null
     */
    public function getOverallMaxPaxAttribute(){
        $outlet_id = $this->id;

        if(is_null($outlet_id)){
            // Outlet still not created
            // Can get its config
            return null;
        }

        $setting_config = Setting::settingsConfig($outlet_id);

        return $setting_config(Setting::OVERALL_MAX_PAX);
    }
    /**
     * Get max_days_in_advance
     * To restrict booking calendar, which days is selectable
     * @return mixed|null
     */
    public function getMaxDaysInAdvanceAttribute(){
        $outlet_id = $this->id;

        if(is_null($outlet_id)){
            // Outlet still not created
            // Can get its config
            return null;
        }

        $buffer_config = Setting::bufferConfig($outlet_id);

        return $buffer_config(Setting::MAX_DAYS_IN_ADVANCE);
    }

}
