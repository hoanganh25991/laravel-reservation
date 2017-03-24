<?php

namespace App\Http\Controllers;

use App\BrandCredit;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use Illuminate\Support\Facades\DB;
use App\Libraries\HoiAjaxCall as Call;
use App\OutletReservationSetting as Setting;

class OutletReservationSettingController extends Controller {
    use ApiResponse;


    public function update(ApiRequest $req){
        $data = json_decode($req->getContent(), JSON_NUMERIC_CHECK);
        $action_type = $data['type'];

        switch($action_type){
            case Call::AJAX_UPDATE_BUFFER:
                $buffer = $data['buffer'];

                foreach($buffer as $key => $value){
                    $config = Setting::where([
                        ['setting_group', Setting::BUFFER_GROUP],
                        ['setting_key', $key]
                    ])->first();
                    
                    if(is_null($config)){
                        $config = new Setting([
                            'setting_group' => Setting::BUFFER_GROUP,
                            'setting_key'   => $key
                        ]);
                    }
                    
                    $config->setting_value = $value;
                    $config->save();
                }
                
                $data = ['buffer' => $this->fetchUpdateBuffer()];
                $code = 200;
                $msg  = Call::AJAX_SUCCESS;
                break;
            case Call::AJAX_UPDATE_NOTIFICATION:
                $notification = $data['notification'];

                foreach($notification as $key => $value){
                    $config = Setting::where([
                        ['setting_group', Setting::NOTIFICATION_GROUP],
                        ['setting_key', $key]
                    ])->first();

                    if(is_null($config)){
                        $config = new Setting([
                            'setting_group' => Setting::NOTIFICATION_GROUP,
                            'setting_key'   => $key
                        ]);
                    }

                    /**
                     * @warn quick sanity data
                     * Need global handle transform
                     */
                    $value = $value === true  ? 1 : $value;
                    $value = $value === false ? 0 : $value;
                    $config->setting_value = $value;
                    $config->save();
                }

                $data = ['notification' => $this->fetchUpdateNotification()];
                $code = 200;
                $msg  = Call::AJAX_SUCCESS;
                break;
            default:
                $data = $req->all();
                $code = 200;
                $msg = Call::AJAX_UNKNOWN_CASE;
                break;
        }

        return $this->apiResponse($data, $code, $msg);
    }
    
    public function fetchUpdateBuffer(){
        $buffer_config = Setting::bufferConfig();
        $buffer_keys   = [
            Setting::MAX_DAYS_IN_ADVANCE,
            Setting::MIN_HOURS_IN_ADVANCE_SLOT_TIME,
            Setting::MIN_HOURS_IN_ADVANCE_SESSION_TIME
        ];
        
        return Setting::buildKeyValueOfConfig($buffer_config, $buffer_keys);
    }

    public function fetchUpdateNotification(){
        $notification_config = Setting::notificationConfig();
        $notification_keys = [
            Setting::SEND_SMS_ON_BOOKING,
            Setting::SEND_SMS_CONFIRMATION,
            Setting::HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM
        ];

        $notification = Setting::buildKeyValueOfConfig($notification_config, $notification_keys);

        $sms_credit_balance = BrandCredit::first()->sms_credit_balance;
        $notification['sms_credit_balance'] = $sms_credit_balance;

        return $notification;
    }
}
