<?php

namespace App\Http\Controllers;

//use App\Session;
use App\Exceptions\DontHavePermissionException;
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
        // Handle post case
        if($req->method() == 'POST'){

            /** @var ReservationUser $user */
            $user = $req->user();

            if(!$user->hasReservationsPermissionOnCurrentOutlet()){
                throw new \Exception('Sorry, current account cant modify reservations page');
            }

            $action_type = $req->json('type');

            $reservation_controller = new ReservationController;

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

                case Call::AJAX_FETCH_RESERVATIONS_BY_DAY:
                    $day_str  = $req->json('day');
                    $reservations = $reservation_controller->fetchReservationsByDay($day_str);
                    
                    $data = compact('reservations');
                    $code = 200;
                    $msg  = Call::AJAX_FETCH_RESERVATIONS_BY_DAY_SUCCESS;

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

        // Handle default case
        // Why have to resolve
        // When staff access admin/reservations
        // No explicit outlet_id told, resolve as the first one
        $this->resolveOutletIdToInject();

        $state = $this->reservationsState();

        return view('admin.reservations')->with(compact('state'));
    }

    private function reservationsState(){
        /** Current logined user */
        // When some actions in page require permission more than
        // what current user assgined
        // Need to know which user permission level
        // To show/hide action
        // Cross check inside controller
        $user = Auth::user();

        $state = [
            'base_url'     => url()->current(),
            'outlet_id'    => Setting::outletId(),
            'reservations' => (new ReservationController)->fetchUpdateReservations(),
            'user'         => $user,
        ];

        return $state;
    }

    /**
     * @param ApiRequest $req
     * @return $this
     * @throws \Exception
     */
    public function getSettingsDashboard(ApiRequest $req){
        // Handle post case
        if($req->method() == 'POST'){

            /** @var ReservationUser $user */
            $user = $req->user();

            if(!$user->hasAdministratorPermissionOnCurrentOutlet()){
                throw new \Exception('Sorry, current account cant modify settings page');
            }

            $action_type        = $req->json('type');
            $session_controller = new SessionController;
            $setting_controller = new SettingController;

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

        // Handle default case
        // Why have to resolve
        // When staff access admin/reservations
        // No explicit outlet_id told, resolve as the first one
        $this->resolveOutletIdToInject();

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

        /** Current logined user */
        // When some actions in page require permission more than
        // what current user assgined
        // Need to know which user permission level
        // To show/hide action
        // Cross check inside controller
        $user = Auth::user();

        $state = [
            'base_url'         => url()->current(),
            'outlets'          => $outlets,
            'outlet_id'        => Setting::outletId(),
            'weekly_sessions'  => $weekly_sessions,
            'special_sessions' => $special_sesssions,
            'buffer'           => $buffer,
            'notification'     => $notification,
            'settings'         => $settings,
            'deposit'          => $deposit,
            'user'             => $user,
        ];

        return $state;
    }

    public function resolveOutletIdToInject(){

        // Only resolve when no outlet_id explicit told
        // Get the first one from
        if(!Setting::isOutletIdSetup()){
            /** @var ReservationUser $user */
            $user = Auth::user();

            if(is_null($user)){
                throw new \Exception('Can\'t find user, are you hack???');
            }

            $outlet_id = $user->allowedOutletIds()->first();

            if(is_null($outlet_id)){
                throw new \Exception('Current account cant access to any outlet');
            }

            Setting::injectOutletId($outlet_id);
        }
    }
}
