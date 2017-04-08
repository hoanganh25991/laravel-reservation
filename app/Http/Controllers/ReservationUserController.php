<?php

namespace App\Http\Controllers;

use App\ReservationUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReservationUserController extends HoiController {
    
    public function fetchUsers(){
        ReservationUser::byBrandId();
        $users = ReservationUser::all();
        return $users;
    }
}
