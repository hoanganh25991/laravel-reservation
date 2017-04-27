<?php

namespace App\Http\Controllers;

use App\Outlet;
use App\Reservation;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use App\Libraries\HoiAjaxCall as Call;
use App\OutletReservationSetting as Setting;

class ReservationController extends HoiController{
    
    use ApiResponse;

    public function resolveBrandIdOutletId(Reservation $reservation){
        //Should try better way to do this
        $outlet_id = $reservation->outlet_id;
        Setting::injectOutletId($outlet_id);

        $outlet    = Outlet::find($outlet_id);
        $brand_id  = $outlet->brand_id;
        Setting::injectBrandId($brand_id);
    }
    
    public function getConfirmPage(ApiRequest $req, Reservation $reservation){
        $this->resolveBrandIdOutletId($reservation);
        /**
         * Customer confirm resrevation
         */
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
            return redirect('');
        }

        //$state = $this->buildAppState($req, $reservation);
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

//    public function buildAppState(ApiRequest $req, Reservation $reservation){
//
//
//        $paypal_token = (new PayPalController)->generateToken();
//
//        $selected_outlet = $reservation->outlet;
//
//        $state = [
//            'base_url'       => url()->current(),
//            'selected_outlet'=> $selected_outlet,
//            'reservation'    => $reservation,
//            'paypal_token'   => $paypal_token,
//        ];
//
//        return $state;
//    }

    /**
     * @param ApiRequest $req
     * @return $this
     */
    public function update(ApiRequest $req){
        $action_type = $req->json('type');

        //Flag to notify context of this function
        //Should resuse it or not
        switch($action_type){
            case Call::AJAX_UPDATE_RESERVATIONS:
                $reservations = $req->json('reservations');

                $validator = null;
                $msg_bag   = collect([]);

                foreach($reservations as $reservation_data){
                    //$validator = Reservation::validateOnCRUD($reservation_data);
                    $validator = Reservation::validateOnCRUD($reservation_data);

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

                    $reservation = Reservation::findOrNew($reservation_data['id']);
                    $reservation->fill($reservation_data);
                    $reservation->save();
                }

                //which means no reservations submit
                if(is_null($validator)){
                    $data = ['reservations' =>  $this->fetchUpdateReservations()];
                    $code = 200;
                    $msg  = Call::AJAX_SUCCESS;
                    break;
                }

                //validate run & fail
                //if($validator->fails()){
                    //$data = $validator->getMessageBag()->toArray();
                if($msg_bag->count() > 0){
                    $data = $msg_bag;
                    $code = 200;
                    $msg  = Call::AJAX_VALIDATE_FAIL;
                    break;
                }

                //everything is fine
                $data = ['reservations' =>  $this->fetchUpdateReservations()];
                $code = 200;
                $msg  = Call::AJAX_SUCCESS;
                break;
            default:
                $data = [];
                $code = 200;
                $msg  = Call::AJAX_UNKNOWN_CASE;
                break;
        }

        return $this->apiResponse($data, $code, $msg);
    }

    public function fetchUpdateReservations(){
        //$reservations = Reservation::last30Days()->where('status', '>=', Reservation::RESERVED)->get();
//        $reservations = Reservation::fromToday()->where('status', '>=', Reservation::RESERVED)->get();
        $reservations = Reservation::fromToday()->where('status', '!=', Reservation::REQUIRED_DEPOSIT)->get();

        return $reservations;
    }
}
