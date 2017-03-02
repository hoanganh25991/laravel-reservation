<?php
namespace App\Traits;

use Carbon\Carbon;
use App\OutletReservationSetting as Setting;
use Hamcrest\Core\Set;
use Illuminate\Support\Collection;

trait ApiUtils{

    protected function getMinutes($time){
        $timeInfo = explode(":", $time);
        $hour = $timeInfo[0];
        $minute = $timeInfo[1];
        
        return $hour * 60 + $minute;
    }

    protected function availableDateRange(){
        $today = Carbon::now(Setting::TIME_ZONE);

        $query_max_day = Setting::where([
            'outlet_id' =>  1,
            'setting_group' => 'BUFFERS',
            'setting_key' => 'MAX_DAYS_IN_ADVANCE'
        ])->first();

        $max_days_in_advance = !is_null($query_max_day) ? $query_max_day : Setting::MAX_DAYS_IN_ADVANCE;


        $max_day = $today->copy()->addDays($max_days_in_advance);
        
        return [$today, $max_day];
    }
    
    public function buildGetKey(){
        return function($key){
            /* @var Collection $this */
            $item = $this->filter(function($i) use($key){return $i->setting_key == $key;})->first();

            /**
             * When no item found, use default config
             */
            if(is_null($item)){
                /**
                 * Should i try catch
                 * Or through exception here
                 * to notify that
                 * they try to access on sth null
                 */
                try{
                    $setting_class = new \ReflectionClass(Setting::class);
                    $item_value = $setting_class->getConstant($key);
                    return $item_value;
                }catch(\Exception $e){

                    return 0;
                }
            }

            $item_value = $item->setting_value;
            switch($item->setting_type){
                case Setting::INT:
                    $item_value = (int) $item_value;
                    break;
            }
            
            return $item_value;
        };
    }
}
