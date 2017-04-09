<?php

namespace App\Http\Controllers\Auth;

use App\ReservationUser;
use App\Traits\HoiAuth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
    use HoiAuth;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct() {
        //ReservationUser used for login check
        //should not limit by brand_id
        //we don't know which brand_id used
        //ReservationUser::$should_scope_by_brand_id = false;
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username() {
        return 'user_name';
    }
    
    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function hoiLogout(){
        Auth::logout();
        
        return redirect()->route('login');
    }
    
}
