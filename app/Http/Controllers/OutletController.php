<?php

namespace App\Http\Controllers;


use App\Outlet;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use App\Libraries\HoiAjaxCall as Call;

class OutletController extends HoiController{

    use ApiResponse;
    
    public function fetchAllOutlet(ApiRequest $req){
        $outlets = Outlet::withoutGlobalScope('brand_id')->where('brand_id', 1)->get();

        if($req->method() == 'POST'){
            $action_type = $req->get('type');
            
            switch($action_type){
                case Call::AJAX_ALL_OUTLETS:
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
        
        if($req->fromApiGroup()){
            $data = $outlets;
            $code = 200;
            $msg  = Call::AJAX_SUCCESS;
            return $this->apiResponse($data, $code, $msg);
        }

        return $outlets;
    }
}
