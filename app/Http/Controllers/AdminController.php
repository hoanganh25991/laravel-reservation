<?php

namespace App\Http\Controllers;

//use App\Session;
use App\Outlet;
use Carbon\Carbon;
use App\ReservationUser;
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
        /** @var ReservationUser $user */
        $user = Auth::user();
        
        if(in_array($outlet_id, $user->allowedOutletIds())){
            session(compact('outlet_id'));
            $data = [];
            $code = 200;
            $msg  = Call::AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS;
        }else{
            $data = [];
            $code = 200;
            $msg  = Call::AJAX_ERROR;
        }

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
            'admin_step'       => 'weekly_sessions_view',
			'deleted_sessions' => [],
			'deleted_timings'  => [],
        ];

        /**
         * Quick check, only handle 1 POST case
         * From ajax 
         * @see Call::AJAX_REFETCHING_DATA
         */
        if($req->method() == 'POST'){
            $data = $state;
            $code = 200;
            $msg  = Call::AJAX_REFETCHING_DATA_SUCCESS;
            return $this->apiResponse($data, $code, $msg);
        }

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

        /**
         * Quick check, only handle 1 POST case
         * From ajax
         * @see Call::AJAX_REFETCHING_DATA
         */
        if($req->method() == 'POST'){
            $data = $state;
            $code = 200;
            $msg  = Call::AJAX_REFETCHING_DATA_SUCCESS;
            return $this->apiResponse($data, $code, $msg);
        }
        
        return view('admin.reservations')->with(compact('state'));
    }
}
