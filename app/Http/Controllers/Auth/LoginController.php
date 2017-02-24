<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/fuck';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Overide to allow user login by user_name
     * @param Request $request
     * @throws \Exception
     */
    protected function validateLogin(Request $request)
    {
        //custom <=> have to return something
        if(($request->has('email') || $request->has('user_name')) && $request->has('password_hash')){
            //ok
            return;
        }
        
        //false case
        throw new \Exception('Fuck you');
        
    }

    protected function credentials(Request $request)
    {
        return $request->only('email', 'user_name', 'password_hash');
    }
}
