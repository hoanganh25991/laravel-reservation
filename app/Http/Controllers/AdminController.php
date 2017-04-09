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
     * AdminController constructor.
     * Brand id implicit inject when get into admin page
     * These function FINE, but not good
     * Current logic base on res_outlet_reservation_user
     * Have to accept _._!
     */
    public function __construct(){
        /**
         * CAN'T access user here
         * BCS after this instance, middleware start run
         * ONLY after middleware run, we have $user
         */
//        $user = Auth::user();
//        $user->injectBrandId();
    }

    /**
     * @return $this
     */
    public function getDashboard(){
        /** @var ReservationUser $user */
        $user = Auth::user();
        //$user->injectBrandId();

        $outlets    = $user->outletsCanAccess();
        $brand_id   = Setting::brandId();

        $state = [
            'outlets'         => $outlets,
            'selected_outlet' => null,
            'base_url'        => url(''),
            'user'            => $user,
            'brand_id'       => $brand_id,
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

        if($user->allowedOutletIds()->contains($outlet_id)){
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function getReservationDashboard(ApiRequest $req){
        /** @var ReservationUser $user */
        //$user = Auth::user();
        //$user->injectBrandId();

        $this->resolveOutletIdToInject();
        /**
         * Reservations
         */
        $reservation_controller = new ReservationController;
        $reservations = $reservation_controller->fetchUpdateReservations();

        $state = [
            'base_url'     => url(''),
            'outlet_id'    => Setting::outletId(),
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

    /**
     * @param ApiRequest $req
     * @return $this
     */
    public function getSettingsDashboard(ApiRequest $req){
        /** @var ReservationUser $user */
        //$user = Auth::user();
        //$user->injectBrandId();

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

        if($req->fromApiGroup()){
            return $this->apiResponse($state, 200, Call::AJAX_SUCCESS);
        }

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

    public function resolveOutletIdToInject(){
        /**
         * These code is DANGEROUS, bcs it base on SESSION
         * which will lose it strength when work standalone with frontend api
         */
        $outlet_id = session()->pull('outlet_id');

        if(is_null($outlet_id)){
            /** @var ReservationUser $user */
            $user = Auth::user();
            $outlet_id = $user->allowedOutletIds()->first();

            if(is_null($outlet_id)){
                throw new \Exception('Can\'t resolve outlet_id to move on');
            }
        }

        Setting::injectOutletId($outlet_id);
    }
}
