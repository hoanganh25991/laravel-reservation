<?php

use App\Http\Requests\ApiRequest;
use Illuminate\Http\Request;
use App\OutletReservationSetting as Setting;
use App\Reservation;
use App\ReservationUser;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any('admin/login', function(ApiRequest $req){
	$user_name = $req->get('user_name');
	$password = $req->get('password');

	//$user = ReservationUser::where('username', $username);

    $logined = Auth::attempt([
        'user_name' => $user_name,
        'password'  => $password
    ], true);

    if($logined){
        return ['msg' => 'ok'];
    }

    return ['msg' => 'fail'];
});

Route::middleware('reservations')->any('admin/reservations', function(){
	Setting::injectBrandId(1);
    Setting::injectOutletId(1);

    $reservations = Reservation::fromToday()->get();

    return $reservations;
});

// Route::any('admin/reservations', function(ApiRequest $req){
//     Setting::injectBrandId(1);
//     Setting::injectOutletId(1);

//     $reservations = Reservation::fromToday()->get();

//     return $reservations;
// });
