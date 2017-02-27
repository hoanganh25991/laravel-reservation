<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\OutletReservationSetting as Setting;
use App\Reservation;
use Carbon\Carbon;
use Validator;
use App\Traits\ApiResponse;

class BookingController extends Controller
{
    use ApiResponse;


    public function dateAvailable(ApiRequest $req){
        /* @var Validator $validator*/
        $validator = Validator::make($req->all(), [
            'outlet_id' => 'required',
            'adult_pax' => 'required',
            'children_pax' => 'required'
        ]);
        
        if($validator->fails()){
            return $this->apiResponse($req->all(), 422, $validator->getMessageBag()->toArray());
        }

//        return $this->apiResponse(['a' => 'coder']);
        //needed info exist, get out session
        //get out config
        //validate which date available

        //GET FIRST which means that
        //may be null
        $query_max_day = Setting::where([
            'outlet_id' =>  1,
            'setting_group' => 'BUFFERS',
            'setting_key' => 'MAX_DAYS_IN_ADVANCE'
         ])->first();

       

        $min_hours_in_advance_slot_time = !is_null($query_min_hours_in_advance_slot_time) ? $query_min_hours_in_advance_slot_time : Setting::MIN_HOURS_IN_ADVANCE_SLOT_TIME;

        $query_min_hours_in_advance_session_time =Setting::where([
            'outlet_id' =>  1,
            'setting_group' => 'BUFFERS',
            'setting_key' => 'MAX_DAYS_IN_ADVANCE'
        ])->first();

        //query on collection return a collection
        //collection has empty array
        $min_hours_in_advance_session_time = !is_null($query_min_hours_in_advance_session_time) ? $query_min_hours_in_advance_session_time : Setting::MIN_HOURS_IN_ADVANCE_SESSION_TIME;

        $query_session = Session::where([
            'one_off' => false,
        ])->orWhere([
            'one_off' => true,
            'one_off_date' => 'ssss' < 'today'
        ])->get();

        //read document for sure what is going on null
        $sessions = !is_null($query_session) ? $query_session : [];

        $today = Carbon::now();

        //compare logic
        //base on timing

        //get out current Reservation in range
        //filter out by these reservation
        $reservations = Reservation::where([
            'reservation_time' < 'valute x',
            'state' => 'xyz'
        ])->get();

        //try to return
        //date > session group > options
        //should group by session name
        //user can pick out one > show up inside





        
    }
    
    
}
