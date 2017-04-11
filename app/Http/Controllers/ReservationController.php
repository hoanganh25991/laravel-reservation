<?php

namespace App\Http\Controllers;

use App\Reservation;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use App\Libraries\HoiAjaxCall as Call;
use App\OutletReservationSetting as Setting;

class ReservationController extends HoiController{
    
    use ApiResponse;
    
    public function getConfirmPage(ApiRequest $req, Reservation $reservation){
        /**
         * Customer confirm resrevation
         */
        if($req->method() == 'POST'){
            $reservation->status = Reservation::CONFIRMED;
            $reservation->save();

            return redirect()->route('reservation_thank_you');
        }
        
        if(is_null($reservation)){
            return redirect('');
        }
        
        $state = $this->buildAppState($reservation);
        
        return view('reservations.confirm-page')->with(compact('state'));
    }

    public function getThankYouPage(){
        return view('reservations.thank-you');
    }

    public function buildAppState(Reservation $reservation){
        //Should try better way to do this
        $outlet_id = $reservation->outlet_id;
        Setting::injectOutletId($outlet_id);

        $paypal_token = (new PayPalController)->generateToken();

        $state = [
            'outlet' => [
                'id' => $reservation->outlet_id,
                'name' => $reservation->outlet_name
            ],

            'pax' => [
                'adult' => $reservation->adult_pax,
                'children' => $reservation->children_pax
            ],

            'reservation' => $reservation,

            'customer' => [
                'salutation' => $reservation->salutation,
                'first_name' => $reservation->first_name,
                'last_name'  => $reservation->last_name,
                'email'      => $reservation->email,
                'phone_country_code' => $reservation->phone_country_code,
                'phone'      => $reservation->phone,
                'remarks'    => $reservation->customer_remarks
            ],
            
            'paypal_token'   => $paypal_token,
            
            'paypal_url'     => url('paypal'),
        ];
        
        return $state;
    }

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

                foreach($reservations as $reservation_data){
                    $validator = Reservation::validateOnCRUD($reservation_data);

                    if($validator->fails()){
                        break;
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
                if($validator->fails()){
                    $data = $validator->getMessageBag()->toArray();
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
        $reservations = Reservation::last30Days()->where('status', '>=', Reservation::RESERVED)->get();
        
        return $reservations;
    }
}
