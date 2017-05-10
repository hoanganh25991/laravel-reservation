<?php

namespace App;
use Illuminate\Database\Eloquent\Builder;
use App\OutletReservationSetting as Setting;

/**
 * @property mixed outlet_name
 * @property mixed id
 * @property mixed paypal_currency
 */
class Outlet extends HoiModel {
    
    protected $table = 'outlet';

    //protected $fillable = [];

    protected $appends = [
        'overall_min_pax',
        'overall_max_pax',
        'max_days_in_advance',
        'send_sms_on_booking',
        'paypal_currency',
        'sms_sender_name',
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
     * @throws \Exception
     */
    public function getOverallMinPaxAttribute(){
        $outlet_id = $this->id;

        if(is_null($outlet_id)){
            // Outlet still not created
            // Can get its config
            throw new \Exception('Outlet doenst have id');
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
     * @throws \Exception
     */
    public function getMaxDaysInAdvanceAttribute(){
        $outlet_id = $this->id;

        if(is_null($outlet_id)){
            // Outlet still not created
            // Can get its config
            throw new \Exception('Outlet doenst have id');
        }

        $buffer_config = Setting::bufferConfig($outlet_id);

        return $buffer_config(Setting::MAX_DAYS_IN_ADVANCE);
    }

    /**
     * Get send_sms_on_booking
     * For better experience with select pax form
     * @return mixed|null
     * @throws \Exception
     */
    public function getSendSMSOnBookingAttribute(){
        $outlet_id = $this->id;

        if(is_null($outlet_id)){
            // Outlet still not created
            // Can get its config
            throw new \Exception('Outlet doenst have id');
        }

        $notification_config = Setting::notificationConfig($outlet_id);

        return $notification_config(Setting::SEND_SMS_ON_BOOKING);
    }

    public function getOutletAddressAttribute($value){
        $address = str_replace("\\n", ", ", $value);

        return $address;
    }

    /**
     * Each outlet has it own paypal currency
     * Payapl Currency match with what currency accepted by the merchant account
     *
     * Bring these info to generate paypal_authorize dynamic
     */
    public function getPaypalCurrencyAttribute(){
        $outlet_id = $this->id;

        if(is_null($outlet_id)){
            // Outlet still not created
            // Can get its config
            throw new \Exception('Outlet doenst have id');
        }

        $deposit_config = Setting::depositConfig($outlet_id);

        return $deposit_config(Setting::PAYPAL_CURRENCY);
    }

    public function getSmsSenderNameAttribute(){
        $outlet_id = $this->id;

        if(is_null($outlet_id)){
            // Outlet still not created
            // Can get its config
            throw new \Exception('Outlet doenst have id');
        }

        $setting_config = Setting::settingsConfig($outlet_id);

        return $setting_config(Setting::SMS_SENDER_NAME);
    }
}
