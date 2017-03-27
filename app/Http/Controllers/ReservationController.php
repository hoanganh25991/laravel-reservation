<?php

namespace App\Http\Controllers;

use App\Reservation;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use App\Libraries\HoiAjaxCall as Call;

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

        $state = [
            'outlet' => [
                'id' => $reservation->outlet_id,
                'name' => $reservation->outlet_name
            ],

            'pax' => [
                'adult' => $reservation->adult_pax,
                'children' => $reservation->children_pax
            ],

            'reservation' => [
                'date' => $reservation->date,
                'time' => $reservation->time,
                'confirm_id' => $reservation->confirm_id
            ],

            'customer' => [
                'salutation' => $reservation->salutation,
                'first_name' => $reservation->first_name,
                'last_name'  => $reservation->last_name,
                'email'      => $reservation->email,
                'phone_country_code' => $reservation->phone_country_code,
                'phone'      => $reservation->phone,
                'remarks'    => $reservation->customer_remarks
            ]
        ];
        
        return $state;
    }

    public function update(ApiRequest $req){
        $data = json_decode($req->getContent(), JSON_NUMERIC_CHECK);

        $action_type = $data['type'];

        switch($action_type){
            case Call::AJAX_UPDATE_RESERVATIONS:
                $reservations = $data['reservations'];

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

                if($validator->fails()){
                    $data = $validator->getMessageBag()->toArray();
                    $code = 200;
                    $msg  = Call::AJAX_VALIDATE_FAIL;
                }else{
                    $data = $this->fetchUpdateReservations();
                    $code = 200;
                    $msg  = Call::AJAX_SUCCESS;
                }
                break;
            default:
                $data = $req->all();
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
