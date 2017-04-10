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

    public function __construct(){}

    /**
     * @param ApiRequest $req
     * @return $this
     */
    public function getDashboard(ApiRequest $req){
        $state = $this->dashboardState();

        return view('admin.index')->with(compact('state'));
    }

    private function dashboardState(){
        /** @var ReservationUser $user */
        $user     = Auth::user();
        $outlets  = $user->outletsCanAccess();

        $state = [
            'outlets'         => $outlets,
            'selected_outlet' => null,
            'base_url'        => url(''),
            'user'            => $user,
        ];

        return $state;
    }

    /**
     * @param ApiRequest $req
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function getReservationDashboard(ApiRequest $req){
        $this->resolveOutletIdToInject();

        $reservation_controller = new ReservationController;

        $action_type = $req->json('type');

        if($req->method() == 'POST'){
            switch($action_type){
                case Call::AJAX_UPDATE_RESERVATIONS:
                    $response = $reservation_controller->update($req);
                    break;
                case Call::AJAX_REFETCHING_DATA:
                    $data = $this->reservationsState();
                    $code = 200;
                    $msg  = Call::AJAX_REFETCHING_DATA_SUCCESS;
                    $response = $this->apiResponse($data, $code, $msg);
                    break;
                default:
                    $data = [];
                    $code = 200;
                    $msg  = Call::AJAX_UNKNOWN_CASE;
                    $response = $this->apiResponse($data, $code, $msg);
                    break;
            }

            return $response;
        }

        $state = $this->reservationsState();

        //Handle get
        return view('admin.reservations')->with(compact('state'));
    }

    private function reservationsState(){
        $reservation_controller = new ReservationController;
        //Build state
        $reservations = $reservation_controller->fetchUpdateReservations();

        $state = [
            'base_url'     => url()->current(),
            'outlet_id'    => Setting::outletId(),
            'reservations' => $reservations
        ];

        return $state;
    }

    /**
     * @param ApiRequest $req
     * @return $this
     */
    public function getSettingsDashboard(ApiRequest $req){
        //Realy important, outlet id should specific which one
        $this->resolveOutletIdToInject();

        $session_controller = new SessionController;
        $setting_controller = new SettingController;

        //implicit get action_type from json call
        $action_type = $req->json('type');

        if($req->method() == 'POST'){
            switch($action_type){
                case Call::AJAX_UPDATE_SESSIONS:
                    $response = $session_controller->update($req);
                    break;
                case Call::AJAX_UPDATE_BUFFER:
                case Call::AJAX_UPDATE_NOTIFICATION:
                case Call::AJAX_UPDATE_SETTINGS:
                case Call::AJAX_UPDATE_DEPOSIT:
                    $response = $setting_controller->update($req);
                    break;
                case Call::AJAX_REFETCHING_DATA:
                    $data = $this->settingsState();
                    $code = 200;
                    $msg  = Call::AJAX_REFETCHING_DATA_SUCCESS;
                    $response = $this->apiResponse($data, $code, $msg);
                    break;
                default:
                    $data = [];
                    $code = 200;
                    $msg  = Call::AJAX_UNKNOWN_CASE;
                    $response = $this->apiResponse($data, $code, $msg);
                    break;

            }

            return $response;
        }

        $state = $this->settingsState();

        return view('admin.settings')->with(compact('state'));
    }

    private function settingsState(){
        $session_controller= new SessionController;
        /**
         * Sessions data
         */
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
        $outlets      = Outlet::all();

        $state = [
            'base_url'         => url()->current(),
            'outlet_id'        => Setting::outletId(),
            'weekly_sessions'  => $weekly_sessions,
            'special_sessions' => $special_sesssions,
            'buffer'           => $buffer,
            'notification'     => $notification,
            'settings'         => $settings,
            'deposit'          => $deposit,
            'outlets'          => $outlets,
        ];

        return $state;
    }

    public function resolveOutletIdToInject(){
        if(!is_null(Setting::$outlet_id)){
            return;
        }
        
        /** @var ReservationUser $user */
        $user = Auth::user();
        if(is_null($user)){
            throw new \Exception('Can\'t find user in admin controller, are you hack???');
        }
        //implicit means he want to go to the first one
        $outlet_id = $user->allowedOutletIds()->first();
        //not outelet set up
        if(is_null($outlet_id)){
            throw new \Exception('Can\'t resolve outlet_id to move on');
        }

        Setting::injectOutletId($outlet_id);
    }
}
