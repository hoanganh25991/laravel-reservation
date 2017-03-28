<?php

namespace App\Http\Controllers;

use App\ReservationUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReservationUserController extends HoiController {
    
    public function fetchUsers(){
        $users = ReservationUser::notAdministrator()->get();
        
        return $users;
    }
}
