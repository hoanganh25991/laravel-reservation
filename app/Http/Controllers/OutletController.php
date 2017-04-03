<?php

namespace App\Http\Controllers;


use App\Outlet;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use App\Libraries\HoiAjaxCall as Call;

class OutletController extends HoiController{

    use ApiResponse;
    
    public function fetchAllOutlet(ApiRequest $req){
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
