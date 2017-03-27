<?php

namespace App\Http\Controllers;

//use App\Session;
use App\Outlet;
use App\ReservationUser;
use Carbon\Carbon;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use Illuminate\Support\Facades\Auth;
use App\Libraries\HoiAjaxCall as Call;
use App\OutletReservationSetting as Setting;
use App\Http\Controllers\OutletReservationSettingController as SettingController;

class AdminController extends HoiController {

    use ApiResponse;

    /**
     * @return $this
     */
    public function getDashboard(){
        /** @var ReservationUser $user */
        $user = Auth::user();
        $outlet_ids = $user->allowedOutletIds();
        $outlets    = Outlet::whereIn('id', $outlet_ids)->get();

        $state = [
            'outlets'         => $outlets,
            'selected_outlet' => null,
            'base_url'        => url('')
        ];

        return view('admin.index')->with(compact('state'));
    }

    /**
     * Outlet id used through session to limit query to DB
     * @param ApiRequest $req
     * @return $this
     */
    public function setUpOuletId(ApiRequest $req){
        $data = json_decode($req->getContent(), true);
        
        $outlet_id = $data['outlet_id'];
        
        session(compact('outlet_id'));
        
        $data = [];
        $code = 200;
        $msg  = Call::AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS;
        
        return $this->apiResponse($data, $code, $msg);
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
        $notification = $setting_controller->fetchUpdateNotifications();
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
     * @param ApiRequest $req
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getReservationDashboard(ApiRequest $req){
        /**
         * Reservations
         */
        $reservation_controller = new ReservationController;
        $reservations = $reservation_controller->fetchUpdateReservations();
        
        $state = [
            'base_url'     => url(''),
            'reservations' => $reservations
        ];
        
        return view('admin.reservations')->with(compact('state'));
    }
}
