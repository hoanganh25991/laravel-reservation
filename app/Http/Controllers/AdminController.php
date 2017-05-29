<?php

namespace App\Http\Controllers;

//use App\Session;
use App\Outlet;
use App\Reservation;
use Carbon\Carbon;
use App\Traits\SendSMS;
use App\ReservationUser;
use App\Traits\ApiResponse;
use App\Events\SentReminderSMS;
use App\Http\Requests\ApiRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Libraries\HoiAjaxCall as Call;
use App\OutletReservationSetting as Setting;
use App\Exceptions\DontHavePermissionException;
use App\Http\Controllers\ReservationController as By;
use App\Http\Controllers\OutletReservationSettingController as SettingController;
use Illuminate\Support\Facades\Log;


class AdminController extends HoiController {

    use ApiResponse;
    use SendSMS;

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
        
        if($req->method() == 'GET'){
            // Handle default case
            // Why have to resolve
            // When staff access admin/reservations
            // No explicit outlet_id told, resolve as the first one
            $this->resolveOutletIdToInject();

            $state = $this->reservationsState();

            return view('admin.reservations')->with(compact('state'));
        }

        // Handle post case
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

            case Call::AJAX_SEARCH_AVAILABLE_TIME:
                // BookinController get data as form-data or url-encoded
                // So... attach form-data or url-encoded from json
                $req_data = $req->json()->all();
                $req->merge($req_data);
                
                // Ask him when available
                $booking_controller = new BookingController();
                $booking_controller->setUpBookingConditions($req);
                $available_time = $booking_controller->availableTime();
                
                // Support make decision for required credit card authorization
                $reservation           = new Reservation($req_data);
                $deposit               = $reservation->deposit;
                $paypal_currency       = $reservation->paypal_currency;
                $payment_authorization = compact('deposit', 'paypal_currency');
                
                $data = compact('available_time', 'payment_authorization');
                $code = 200;
                $msg  = Call::AJAX_AVAILABLE_TIME_FOUND;

                $response = $this->apiResponse($data, $code, $msg);
                break;

            case Call::AJAX_CREATE_NEW_RESERVATION:
                // BookinController get data as form-data or url-encoded
                // So... attach form-data or url-encoded from json
                $req_data = $req->json()->all();
                $req->merge($req_data);
                
                // Ask him to solve the reservation book case
                $booking_controller = new BookingController();

                $validator = Reservation::validateOnCRUD($req->all());
                // Validate fail
                if($validator->fails()){
                    $data = $validator->getMessageBag()->toArray();
                    $code = 422;
                    $msg  = Call::AJAX_RESERVATION_VALIDATE_FAIL;

                    $response = $this->apiResponse($data, $code, $msg);
                    break;
                }

                // If booking out of overall min|max pax
                if(!$booking_controller->bookingInOverallRange($req)){
                    $data = ['pax' => 'total pax out of overall_range'];
                    $code = 422;
                    $msg  = Call::AJAX_RESERVATION_VALIDATE_FAIL;

                    $response = $this->apiResponse($data, $code, $msg);
                    break;
                }

                /**
                 * Recheck if customer with reservation info still available
                 * Customer may search through any condition
                 * But only Submit hit, info send
                 * In that longtime, not sure reservation still available
                 */
                if(!$booking_controller->bookingStillAvailable($req)){
                    $data = [];
                    $code = 422;
                    $msg  = Call::AJAX_RESERVATION_NO_LONGER_AVAILABLE;

                    $response = $this->apiResponse($data, $code, $msg);
                    break;
                }

                // Everythign is fine
                // Create reservation
                $reservation = new Reservation($req_data);
                // Create reservation INSIDE ADMIN PAGE
                // Check reservation authorization base on 'Admin decision'
                $required_credit_card_authorization   = $req->json('required_credit_card_authorization');
                $should_ask_for_payment_authorization = $reservation->requiredDeposit()
                                                        && $required_credit_card_authorization;
                $status = $should_ask_for_payment_authorization ?
                                Reservation::REQUIRED_DEPOSIT
                                : Reservation::RESERVED;
                // Update status
                $reservation->status = $status;
                // Store reservation
                $reservation->save();

                // Should sent SMS immediately
                // Dont wait for cron-jobs, when reservation status updated
                // No duplicate task on send SMS
                $should_send = $req->json('sms_message_on_reserved');

                if($should_send){
                    $telephone   = $reservation->full_phone_number;
                    $message     = $should_ask_for_payment_authorization ?
                                        $reservation->confirmation_sms_ask_payment_authorization_message
                                        :$reservation->sms_message_on_reserved;
                    $sender_name = Setting::smsSenderName();

                    $success_sent = $this->sendOverNexmo($telephone, $message, $sender_name);

                    if($success_sent === true && !$should_ask_for_payment_authorization){
                        Log::info('Success send sms to reminder');
                        //event(new SentReminderSMS($reservation));
                        $reservation->status = Reservation::REMINDER_SENT;
                        $reservation->save();
                    }else{
                        $error_info = $success_sent;
                        Log::info($error_info);
                    }
                }

                $data = compact('reservation');
                $code = 200;
                $msg  = Call::AJAX_RESERVATION_SUCCESS_CREATE;

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
            'outlet'       => Outlet::find(Setting::outletId()),
            'reservations' => (new ReservationController)->fetchReservationsByDay(By::TODAY),
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
