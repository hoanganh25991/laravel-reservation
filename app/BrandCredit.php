<?php

namespace App;

use App\OutletReservationSetting as Setting;

/**
 * @property mixed $unlimited_sms
 * @property mixed $sms_credit_balance
 */
class BrandCredit extends HoiModel {
    const SMS = 'SMS';

    const GOD_MODE = 1;

    protected $table = 'brand_credit';

    protected $guarded = ['id'];

    /**
     * Protect model from unwanted column when build query
     */
    protected $fillable = [
        //'brand_id', //modified on boot
        'sms_credit_balance',
        'sms_credit_consumed',
        'email_credit_balance',
        'email_credit_consumed',
        'unlimited_sms',
        'unlimited_email',
    ];

    /**
     * Inject into boot process
     * To modify on query scope or
     * Listen eloquent event : creating, saving, updating,...
     */
    protected static function boot(){
        parent::boot();

        self::creating(function(BrandCredit $brand_credit){
            $brand_credit->attributes['brand_id'] = Setting::brandId();
        });

        /**
         * Any query limited to specific brand_id
         */
        static::byBrandId();
    }

    /**
     * When sms sent
     * increase consumed sms
     * decrease balance
     *
     * when unlimited flag on
     * don't update balance
     * (as GOD MODE)
     */
    public function updateSMSCredit(){
        $brand_credit = BrandCredit::firstOrNew([
            'brand_id' => Setting::brandId()
        ]);

        /**
         * Increase consumed
         */
        $brand_credit->sms_credit_consumed++;
        
        /**
         * Decrease balance
         */
        if(!$this->inGodMod(BrandCredit::SMS)){
            $brand_credit->sms_credit_balance--;
        }
        
        $brand_credit->save();
    }

    /**
     * Check god mod of type X
     * @param string $type
     * @return bool
     */
    public function inGodMod($type = null){
        switch($type){
            case BrandCredit::SMS:
                return $this->unlimited_sms == BrandCredit::GOD_MODE;
            default:
                return false;
        }
    }


}
