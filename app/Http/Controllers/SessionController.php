<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SessionController extends HoiController{

    use ApiResponse;


    public function update(){
        return $this->apiResponse('hello world', 200, 'vkl');
    }
}
