<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\OutletReservationSetting as Setting;
use App\Session;
use App\Traits\ApiResponse;

class AdminController extends HoiController {

    use ApiResponse;

    public function getDashboard(){
        return view('admin.index');
    }

    /**
     * @param ApiRequest $req
     * @return $this
     */
    public function getSettingsDashboard(ApiRequest $req){
        /**
         * Sessions data
         */
        $weekly_sessions   = Session::normalSession()->get();
        $special_sesssions = Session::allSpecialSession()->get();

        /**
         * Config data
         */
        $buffer_config = Setting::bufferConfig();
        $buffer_keys   = [
            Setting::MAX_DAYS_IN_ADVANCE,
            Setting::MIN_HOURS_IN_ADVANCE_SLOT_TIME,
            Setting::MIN_HOURS_IN_ADVANCE_SESSION_TIME
        ];

        $notification_config = Setting::notificationConfig();
        $notification_keys = [
            Setting::SEND_SMS_ON_BOOKING,
            Setting::SEND_SMS_CONFIRMATION,
            Setting::HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM
        ];

        $settings_config = Setting::settingsConfig();
        $settings_keys = [
            Setting::BRAND_ID,
            Setting::SMS_SENDER_NAME
        ];

        $state = [
            'weekly_sessions'   => $weekly_sessions,
            'special_sesssions' => $special_sesssions,
            'buffer'            => Setting::buildKeyValueOfConfig($buffer_config, $buffer_keys),
            'notifcation'       => Setting::buildKeyValueOfConfig($notification_config, $notification_keys),
            'settings'          => Setting::buildKeyValueOfConfig($settings_config, $settings_keys),
        ];

        return view('admin.settings')->with(compact('state'));
    }
}
