<?php

namespace App\Http\Controllers;


use App\Outlet;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use App\Libraries\HoiAjaxCall as Call;
use App\OutletReservationSetting as Setting;

class OutletController extends HoiController{

    use ApiResponse;
    
    public function fetchAllOutlet(ApiRequest $req){
        $brand_id = $req->get('brand_id');

        if(is_null($brand_id)){
            throw new \Exception('Please submit brand_id');
        }

        Setting::injectBrandId($brand_id);

        $outlets = Outlet::all();

        if($req->method() == 'POST'){
            $action_type = $req->get('type');
            
            switch($action_type){
                case Call::AJAX_ALL_OUTLETS:
                    $data = [
                        'outlets' => $outlets
                    ];
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
            $data = [
                'outlets' => $outlets
            ];
            $code = 200;
            $msg  = Call::AJAX_SUCCESS;
            return $this->apiResponse($data, $code, $msg);
        }

        return $outlets;
    }
}
