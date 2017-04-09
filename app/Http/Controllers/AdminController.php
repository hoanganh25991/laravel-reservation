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
     * @return $this
     */
    public function getDashboard(){
        /** @var ReservationUser $user */
        $user     = Auth::user();
        $outlets  = $user->outletsCanAccess();

        $state = [
            'outlets'         => $outlets,
            'selected_outlet' => null,
            'base_url'        => url(''),
            'user'            => $user,
        ];

        return view('admin.index')->with(compact('state'));
    }

    /**
     * Outlet id used through session to limit query to DB
     * @param ApiRequest $req
     * @return $this
     */
    public function setUpOuletId(ApiRequest $req){
        /** @var ReservationUser $user */
        $user      = Auth::user();
        $outlet_id = $req->json('outlet_id');

        $has_permission_on_outlet = $user->allowedOutletIds()->contains($outlet_id);

        if($has_permission_on_outlet){
            session(compact('outlet_id'));
            $data = [];
            $code = 200;
            $msg  = Call::AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS;
        }else{
            $data = [];
            $code = 422;
            $msg  = Call::AJAX_UPDATE_SCOPE_OUTLET_ID_ERROR;
        }

        return $this->apiResponse($data, $code, $msg);
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

        if(!is_null($action_type)){
            switch($action_type){
                case Call::AJAX_UPDATE_RESERVATIONS:
                    $api_data = $reservation_controller->update($req);
                case Call::AJAX_REFETCHING_DATA:
                    break;
                default:
                    $api_data = $reservation_controller->update($req);
                    break;
            }
        }

        //Build state
        $reservations = $reservation_controller->fetchUpdateReservations();

        $state = [
            'base_url'     => $req->url(),
            'outlet_id'    => Setting::outletId(),
            'reservations' => $reservations
        ];

        if(isset($api_data) && $api_data['used']){
            //Response as what api_data help us build
            return $this->apiResponse($api_data['data'], $api_data['code'], $api_data['msg']);
        }

        if($req->method() == 'POST'){
            $data = $state;
            $code = 200;
            $msg  = Call::AJAX_SUCCESS;
            return $this->apiResponse($data, $code, $msg);
        }

        //Handle get
        return view('admin.reservations')->with(compact('state'));
    }

    /**
     * @param ApiRequest $req
     * @return $this
     */
    public function getSettingsDashboard(ApiRequest $req){
        //Realy important, outlet id should specific which one
        $this->resolveOutletIdToInject();
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
        $outlets      = Outlet::all();
    
        $state = [
            'base_url'         => url(''),
            'outlet_id'        => Setting::outletId(),
            'weekly_sessions'  => $weekly_sessions,
            'special_sessions' => $special_sesssions,
            'buffer'           => $buffer,
            'notification'     => $notification,
            'settings'         => $settings,
            'deposit'          => $deposit,
            'admin_step'       => 'weekly_sessions_view',
			'deleted_sessions' => [],
			'deleted_timings'  => [],
            'outlets'          => $outlets,
        ];

        return view('admin.settings')->with(compact('state'));
    }

    public function resolveOutletIdToInject(){
        /**
         * Pull from session means get & delete
         */
        //$outlet_id = session()->pull('outlet_id');
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
