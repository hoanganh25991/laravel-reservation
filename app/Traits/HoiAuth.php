<?php
namespace App\Traits;

use Illuminate\Support\Facades\Auth as IlluminateAuth;

trait HoiAuth {
    /**
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function redirectTo(){
        /** @var ReservationUser $user */
//        $user = IlluminateAuth::user();
//        if($user->canAccessAdminPage()){
//            return route('admin');
//        }
//
//        return url('');
        return route('admin');
    }
}