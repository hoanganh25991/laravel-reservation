<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\OutletReservationSetting as Setting;
use App\Session;
use App\Traits\ApiResponse;
use Carbon\Carbon;

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
        $weekly_sessions   = Session::normalSession()->with('timings')->get();
        $special_sesssions = Session::allSpecialSession()->with('timings')->get();

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

        /**
         * Rebuild weekly session view for weekly session
         * User need to see session in single day of week
         */
        $weekly_sessions_view = $this->_buildWeeklySessionsView($weekly_sessions);

        //dd($weekly_sessions_view);

        $state = [
            'weekly_view'         => $weekly_sessions_view,
            'weekly_sessions'     => $weekly_sessions,
            'special_sessions'    => $special_sesssions,
            'buffer'              => Setting::buildKeyValueOfConfig($buffer_config, $buffer_keys),
            'notifcation'         => Setting::buildKeyValueOfConfig($notification_config, $notification_keys),
            'settings'            => Setting::buildKeyValueOfConfig($settings_config, $settings_keys),
        ];

        return view('admin.settings')->with(compact('state'));
    }

    /**
     * Session for view group by date
     * Session for edit should relfect what store in DB
     * @param Collection $weekly_sessions
     */
    public function _buildWeeklySessionsView($weekly_sessions = null){
        if(is_null($weekly_sessions)){
            $weekly_sessions = collect([]);
        }

        $today = Carbon::now(Setting::timezone());
        $monday = $today->copy()->startOfWeek();
        $sunday = $today->copy()->endOfWeek();

        $date_range = [$monday, $sunday];

        $weekly_sessions_view =
            $weekly_sessions
                ->map->assignDate($date_range)->collapse()
                ->groupBy(function($session){return $session->date->format('l');})
//                ->sortBy(function($group, $group_name){
//                    switch($group_name){
//                        case 'Monday':
//                            return Carbon::MONDAY;
//                        case 'Tuesday':
//                            return Carbon::TUESDAY;
//                        case 'Wednesday':
//                            return Carbon::WEDNESDAY;
//                        case 'Thursday':
//                            return Carbon::THURSDAY;
//                        case 'Friday':
//                            return Carbon::FRIDAY;
//                        case 'Saturday':
//                            return Carbon::SATURDAY;
//                        case 'Sunday':
//                            return 7 + Carbon::SUNDAY;
//                        default:
//                            return 0;
//                    }
//                })
                ;
        
        return $weekly_sessions_view;
    }
}
