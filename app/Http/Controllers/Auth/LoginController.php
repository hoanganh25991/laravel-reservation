<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\ApiRequest;
use App\Traits\HoiAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller {

    use AuthenticatesUsers;
    use HoiAuth;

    /**
     * Create a new controller instance.
     */
    public function __construct(){ }

    /**
     * Get the login username to be used by the controller.
     * @return string
     */
    public function username(){
        return 'user_name';
    }

    public function hoiLogin(ApiRequest $req){
        if($req->method() == 'POST'){
            return $this->login($req);
        }

        return $this->showLoginForm();
    }

    /**
     * Log the user out of the application.
     * @return \Illuminate\Http\Response
     */
    public function hoiLogout(){
        Auth::logout();

        return redirect()->route('login');
    }

}
