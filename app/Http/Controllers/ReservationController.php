<?php

namespace App\Http\Controllers;

use App\Mail\EmailOnCharge;
use App\Mail\EmailOnVoid;
use App\Outlet;
use Carbon\Carbon;
use App\Reservation;
use App\Traits\SendSMS;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use Illuminate\Support\Facades\Mail;
use App\Libraries\HoiAjaxCall as Call;
use App\OutletReservationSetting as Setting;
use App\Http\Controllers\ReservationController as By;

class ReservationController extends HoiController{

    // Fetch reservations by day
    const TODAY        = 'TODAY';
    const NEXT_3_HOURS = 'NEXT_3_HOURS';
    const TOMORROW     = 'TOMORROW';
    const NEXT_3_DAYS  = 'NEXT_3_DAYS';
    const NEXT_7_DAYS  = 'NEXT_7_DAYS';
    const NEXT_30_DAYS = 'NEXT_30_DAYS';

    use ApiResponse;
    use SendSMS;

    public function resolveBrandIdOutletId(Reservation $reservation){
        //Should try better way to do this
        $outlet_id = $reservation->outlet_id;
        Setting::injectOutletId($outlet_id);

        $outlet    = Outlet::find($outlet_id);
        $brand_id  = $outlet->brand_id;
        Setting::injectBrandId($brand_id);
    }

    public function apiConfirmPage(ApiRequest $req){
        
        if($req->method() == 'POST'){

            $action_type = $req->get('type');
            
            switch($action_type){
                case Call::AJAX_FIND_RESERVATION:
                    $confirm_id  = $req->get('confirm_id');
                    $reservation = Reservation::findByConfirmId($confirm_id);
                    $outlet      = Outlet::find($reservation->outlet_id);

                    // Inject paypal_token
                    Setting::injectOutletId($reservation->outlet_id);
                    $paypal_token = (new PayPalController)->generateToken();

                    // New logic on reservation can be edit by customer
                    /**
                     * New setting check for if customer can edit the reservation
                     */
                    $allowed_edit_by_customer = $reservation->allowedEditByCustomer();

                    // Build response
                    $data = compact('reservation', 'outlet', 'paypal_token', 'allowed_edit_by_customer');
                    $code = 200;
                    $msg  = Call::AJAX_FIND_RESERVATION_SUCCESS;
                    
                    $response = $this->apiResponse($data, $code, $msg);
                    break;

                case Call::AJAX_CONFIRM_RESERVATION:
                    $confirm_id  = $req->get('confirm_id');
                    $reservation = Reservation::findByConfirmId($confirm_id);
                    $outlet      = Outlet::find($reservation->outlet_id);

                    // Only change status of reservation to CONFIRMED
                    // When reservation booked
                    if($reservation->status >= Reservation::RESERVED){
                        $reservation->status = Reservation::CONFIRMED;
                        $reservation->save();

                        $data = compact('reservation', 'outlet');
                        $code = 200;
                        $msg  = Call::AJAX_CONFIRM_RESERVATION_SUCCESS;
                        
                        $response = $this->apiResponse($data, $code, $msg);
                        break;
                    }

                    // Reservation not changed to confirm
                    // It should be RESERVED first
                    $data = compact('reservation', 'outlet');
                    $code = 422;
                    $msg  = Call::AJAX_RESERVATION_STILL_NOT_RESERVED;

                    $response = $this->apiResponse($data, $code, $msg);
                    break;
                
                case Call::AJAX_CANCEL_RESERVATION:
                    $confirm_id  = $req->get('confirm_id');
                    $reservation = Reservation::findByConfirmId($confirm_id);
                    $outlet      = Outlet::find($reservation->outlet_id);

                    // Only change status of reservation to CONFIRMED
                    // When reservation booked
                    $reservation->status = Reservation::USER_CANCELLED;
                    $reservation->save();

                    $data = compact('reservation', 'outlet');
                    $code = 200;
                    $msg  = Call::AJAX_CANCEL_RESERVATION_SUCCESS;

                    $response = $this->apiResponse($data, $code, $msg);
                    break;

                default:
                    $data = [];
                    $code = 422;
                    $msg  = Call::AJAX_UNKNOWN_CASE;
                    $response = $this->apiResponse($data, $code, $msg);
                    break;
            }
            
            return $response;
        }
    }
    
    public function getConfirmPage(ApiRequest $req, Reservation $reservation){
        // Self inject brand_id, outlet_id
        $this->resolveBrandIdOutletId($reservation);

        if($req->method() == 'POST'){
            
            $action_type = $req->get('type');

            // $response built when this controller
            // base on other controller to handle request
            // WHY??? same end point idea
            // GET|POST at one place
            $response = null;
            
            switch($action_type){
                

                case Call::AJAX_PAYMENT_REQUEST:
                    $response = (new PayPalController)->handlePayment($req);
                    break;

                case Call::AJAX_CONFIRM_RESERVATION:
                    // Only change status of reservation to CONFIRMED
                    // When reservation booked
                    if($reservation->status >= Reservation::RESERVED){
                        $reservation->status = Reservation::CONFIRMED;
                        $reservation->save();

                        $data = compact('reservation');
                        $code = 200;
                        $msg  = Call::AJAX_CONFIRM_RESERVATION_SUCCESS;
                        break;
                    }

                    // Reservation not changed to confirm
                    // It should be RESERVED first
                    $data = compact('reservation');
                    $code = 422;
                    $msg  = Call::AJAX_RESERVATION_STILL_NOT_RESERVED;
                    break;

                default:{
                    $data = [];
                    $code = 422;
                    $msg  = Call::AJAX_UNKNOWN_CASE;
                    break;
                }
            }
            
            if($response) return $response;

            return $this->apiResponse($data, $code, $msg);
        }
        
        if(is_null($reservation)){
            throw new \Exception("Sorry, we cant find your reservation.");
        }

        // State for this page
        $paypal_token    = (new PayPalController)->generateToken();
        $selected_outlet = $reservation->outlet;

        $state = [
            'base_url'       => url()->current(),
            'selected_outlet'=> $selected_outlet,
            'reservation'    => $reservation,
            'paypal_token'   => $paypal_token,
            'thank_you_url'  => route('reservation_thank_you')
        ];
        
        return view('reservations.confirm-page')->with(compact('state'));
    }

    public function getThankYouPage(){
        return view('reservations.thank-you');
    }

    /**
     * @param ApiRequest $req
     * @return $this
     */
    public function update(ApiRequest $req){
        $action_type = $req->json('type');

        //Flag to notify context of this function
        //Should resuse it or not
        $response = null;

        switch($action_type){
            case Call::AJAX_UPDATE_RESERVATIONS:
                $reservations = $req->json('reservations');

                $validator = null;
                $msg_bag   = collect([]);

                $reservations_id = collect([]);

                foreach($reservations as $reservation_data){
                    $reservations_id->push($reservation_data['id']);
                    //$validator = Reservation::validateOnCRUD($reservation_data);
                    $validator = Reservation::validateOnCRUDInAdminPage($reservation_data);

                    if($validator->fails()){
                        //break;
                        // Instead of break here, we continue save which one is fine
                        // Store error msg in msg_bag
                        $first_fail_msg = $validator->getMessageBag()->first();
                        // Build readable error msg
                        $confirm_id = $reservation_data['confirm_id'];
                        $error_msg  = "Reservation No. $confirm_id: $first_fail_msg";
                        $msg_bag->push($error_msg);
                        // Move on
                        continue;
                    }

//                    $reservation = Reservation::findOrNew($reservation_data['id']);
                    /* @var Reservation $reservation */
                    $reservation = Reservation::find($reservation_data['id']);
                    $lastPaymentStatus = $reservation->payment_status;
                    $lastReservationStatus = $reservation->status;
                    if(is_null($reservation)){
                        $reservation_id = $reservation_data['id'];
                        $error_msg = "Reservation id. $reservation_id: Cant find";
                        $msg_bag->push($error_msg);
                        // Move on
                        continue;
                    }

                    $reservation->fill($reservation_data);
                    $reservation->save();
                    $justVoided = $lastPaymentStatus == Reservation::PAYMENT_PAID
                                  && $reservation->payment_status == Reservation::PAYMENT_REFUNDED;

                    if($justVoided){
                        // Notify user what happen both SMS & email
                        $telephone   = $reservation->full_phone_number;
                        $message     = $reservation->void_msg;
                        $sender_name = Setting::smsSenderName();

                        $success_sent = $this->sendOverNexmo($telephone, $message, $sender_name);;

                        // Send email
                        $customer_name = "$reservation->salutation $reservation->first_name $reservation->last_name";
                        $customer      = (object)['email' => $reservation->email, 'name' => $customer_name];
                        try{
                            Mail::to($customer)->send(new EmailOnVoid($reservation));
                        }catch(\Exception $e){}
                    }

                    $justCharged = $lastPaymentStatus == Reservation::PAYMENT_PAID
                                  && $reservation->payment_status == Reservation::PAYMENT_CHARGED;
                    if($justCharged){
                        // Notify user what happen both SMS & email
                        $telephone   = $reservation->full_phone_number;
                        $message     = $reservation->charge_msg;
                        $sender_name = Setting::smsSenderName();

                        $success_sent = $this->sendOverNexmo($telephone, $message, $sender_name);;

                        // Send email
                        $customer_name = "$reservation->salutation $reservation->first_name $reservation->last_name";
                        $customer      = (object)['email' => $reservation->email, 'name' => $customer_name];
                        try{
                            Mail::to($customer)->send(new EmailOnCharge($reservation));
                        }catch(\Exception $e){}
                    }

                    $justStaffCancelled = $lastPaymentStatus != Reservation::STAFF_CANCELLED
                                         && $reservation->status == Reservation::STAFF_CANCELLED;

                    if($justStaffCancelled){
                        $reservation->autoSendSMSEmailConfirmWhenUserCancel();
                    }
                }

                //which means no reservations submit
                if(is_null($validator)){
                    $data = ['reservations' =>  $this->fetchUpdateReservations($reservations_id)];
                    $code = 200;
                    $msg  = Call::AJAX_SUCCESS;
                    break;
                }

                //validate run & fail
                //if($validator->fails()){
                    //$data = $validator->getMessageBag()->toArray();
                if($msg_bag->count() > 0){
                    $data = $msg_bag;
                    $code = 422;
                    $msg  = Call::AJAX_VALIDATE_FAIL;
                    $errorMsg = $msg_bag->implode(",");
                    $response = $this->errorResponse($data, $code, $msg, $errorMsg);
                    break;
                }

                //everything is fine
                $data = ['reservations' =>  $this->fetchUpdateReservations($reservations_id)];
                $code = 200;
                $msg  = Call::AJAX_SUCCESS;
                break;
            default:
                $data = [];
                $code = 200;
                $msg  = Call::AJAX_UNKNOWN_CASE;
                break;
        }

        if($response){
            return $response;
        }

        return $this->apiResponse($data, $code, $msg);
    }

    public function fetchUpdateReservations($reservations_id = null){
        if(is_null($reservations_id)){
            return $this->fetchReservationsByDay(By::TODAY);
        }

        return Reservation::whereIn('id', $reservations_id)->get();
    }

    public function fetchReservationsByRangeDay($range_day_str){
        try{
            $range_day = json_decode($range_day_str);

            $start_day = Carbon::createFromFormat('Y-m-d', $range_day[0], Setting::timezone())->setTime(0, 0, 0);
            $end_day   = Carbon::createFromFormat('Y-m-d', $range_day[1], Setting::timezone())->setTime(0, 0, 0)->addDays(1);
            $reservations = Reservation::notRequiredDeposit()->byDayBetween($start_day, $end_day)->get();

            return $reservations;
            
        }catch(\Exception $e){
            $msg  = "Please submit day_str as json on array, ex: [\"2017-07-11\",\"2017-07-12\"]. If you want custom range day.";
            $msg .= $e->getMessage();
            
            return new \Exception($msg);
        }
    }

    public function fetchReservationsByDay($day_str = null){

        $start_day = Carbon::today(Setting::timezone())->setTime(0, 0, 0);
        $method    = 'addDays';
        $exception = null;

        switch($day_str){
            // Consider nothing submit as fetch by today
            case null:
            case By::TODAY:
                $add_up_val = 1;
                break;

            case By::NEXT_3_HOURS:
                $start_day = Carbon::now(Setting::timezone());
                $add_up_val = 4;
                $method = 'addHours';
                break;

            case By::TOMORROW:
                // as tomorrow case, start day is early of tomorrow
                // ok, at one more
                $start_day = $start_day->copy()->addDays(1);
                $add_up_val = 1;
                break;

            case By::NEXT_3_DAYS:
                // why at 4 in 3_days case
                // bcs we want to reach up to 23:59:59
                // when filter in between as [)
                // equal at first start
                // less than at last end
                $add_up_val = 4;
                break;

            case By::NEXT_7_DAYS:
                $add_up_val = 8;
                break;

            case By::NEXT_30_DAYS:
                $add_up_val = 31;
                break;

            // Default case
            // Handle on random pick day from staff
            default:
                try{
                    $start_day = Carbon::createFromFormat('Y-m-d', $day_str, Setting::timezone());
                    // Unlinke other days wrapper, Carbon inject current hours, minutes, seconds
                    // Into Y-m-d format, so explicit check it back to first of day
                    $start_day->setTime(0, 0, 0);
                    $add_up_val  = 1;
                }catch(\Exception $e){
                    $msg  = "Please submit day_str as format YYYY-MM-DD, ex: 2017-07-11. Fail to parse submited day_str: $day_str. If you want single custom day.";
                    $msg .= $e->getMessage();
                    $exception = new \Exception($msg);
                }
                break;
        }


        // Try to parse in case of exception
        // Bcs now we can handle day_str as range-day [2017-07-11, 2017-07-12]
        if($exception){
            // Try with another parse case
            $reservations = $this->fetchReservationsByRangeDay($day_str);

            if($reservations instanceof \Exception){
                $nl   = PHP_EOL;
                $msg  = "$exception->getMessage()$nl";
                $msg .= "$reservations->getMessage()$nl";
                // Can not try catch any more
                // Throw it out ^^
                throw new \Exception($msg);
            }

            return $reservations;
        }

        // Query reservation in between of start & end
        $end_day = $start_day->copy()->$method($add_up_val);

        //$reservations = Reservation::alreadyReserved()->byDayBetween($start_day, $end_day)->get();
        //$reservations = Reservation::byDayBetween($start_day, $end_day)->get();
        // Currently we not support a booking require ahthorization but still not pay
        // Obmit these reservation from fetch to admin page
        $reservations = Reservation::notRequiredDeposit()->byDayBetween($start_day, $end_day)->get();

        return $reservations;
    }

    public function findReservation($search_term){
        try{
            $reservation = Reservation::findByConfirmIdScopeOutlet($search_term);
            $reservations = [$reservation];
        }catch(\Exception $e){
            // Cant find reservation by confirm_id, just means that
            // We should find it by other try
            $reservations = Reservation::namePhoneEmailLikeSearchTerm($search_term)->get();
        }

        return $reservations;
    }
}
