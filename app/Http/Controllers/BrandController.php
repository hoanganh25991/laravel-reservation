<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Libraries\HoiAjaxCall as Call;

class BrandController extends HoiController
{
    use ApiResponse;

    public function fetchBrands(){
        $brands = Brand::all();

        $data = compact('brands');
        $code = 200;
        $msg  = Call::AJAX_SUCCESS;

        return $this->apiResponse($data, $code, $msg);
    }
}
