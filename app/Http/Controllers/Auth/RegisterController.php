<?php

namespace App\Http\Controllers\Auth;

use App\ReservationUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;
    /**
     * Create a new controller instance.
     *
     */
    public function __construct() {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
            'user_name'    => 'required|max:255|unique:outlet_reservation_user',
            'email'        => 'required|email|max:255',
            'password'     => 'required|min:6|confirmed',
            'display_name' => 'required',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data) {
        return ReservationUser::create([
            'user_name'     => $data['user_name'],
            'password_hash' => bcrypt($data['password']),
            'email'         => $data['email'],
            'display_name'  => $data['display_name'],
        ]);
    }

    public function redirectTo(){
//        return route('admin');
        return redirect()->back();
    }
}
