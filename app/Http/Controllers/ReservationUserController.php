<?php

namespace App\Http\Controllers;

use App\ReservationUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReservationUserController extends HoiController {
    
    public function fetchUsers(){
//        $users = ReservationUser::notAdministrator()->get();
//        $users = ReservationUser::notCurrentUser()->get();
        $users = ReservationUser::all();
//        $current_user = Auth::user();
//
//        if(!is_null($current_user)){
//            $users
//                ->sortBy(function($user) use ($current_user){
//                    if($user->id ==  $current_user->id){
//                        return 1;
//                    }
//
//                    return 0;
//                });
//
//            return $users;
//        }

        return $users;
    }
}
