<?php

namespace App\Http\Controllers;


use App\Outlet;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use App\Libraries\HoiAjaxCall as Call;

class OutletController extends HoiController{

    use ApiResponse;
    
    public function fetchAllOutlet(ApiRequest $req){
        if($req->method() == 'POST'){
            $action_type = $req->get('type');
            
            switch($action_type){
                case Call::AJAX_ALL_OUTLETS:
                    $outlets = Outlet::all();
                    $data = $outlets;
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
        
        
        $outlets = Outlet::all();

        if($req->fromApiGroup()){
            $data = $outlets;
            $code = 200;
            $msg  = Call::AJAX_SUCCESS;
            return $this->apiResponse($data, $code, $msg);
        }


        return $outlets;
    }
}
