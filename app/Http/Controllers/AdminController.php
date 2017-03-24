<?php

namespace App\Http\Controllers;

//use App\Session;
use Carbon\Carbon;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use App\OutletReservationSetting as Setting;
use App\Http\Controllers\OutletReservationSettingController as SettingController;

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
        $session_controller= new SessionController;
        $weekly_sessions   = $session_controller->fetchUpdatedWeeklySessions();
        $special_sesssions = $session_controller->fetchUpdatedSpecialSessions();

        /**
         * Config data
         */
        $setting_controller=new SettingController;
        $buffer       = $setting_controller->fetchUpdateBuffer();
        $notification = $setting_controller->fetchUpdateNotification();
        $settings     = $setting_controller->fetchUpdateSettings();
        $deposit      = $setting_controller->fetchUpdateDeposit();
    
        $state = [
            'base_url'         => url(''),
            'weekly_sessions'  => $weekly_sessions,
            'special_sessions' => $special_sesssions,
            'buffer'           => $buffer,
            'notification'     => $notification,
            'settings'         => $settings,
            'deposit'          => $deposit,
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
//                ->groupBy(function($session){return $session->date->format('l');})
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
