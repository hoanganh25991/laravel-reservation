<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();
Route::get( '', 'BookingController@getBookingForm');
Route::post('', 'BookingController@getBookingForm');

Route::get( 'reservations/thank-you',    'ReservationController@getThankYouPage')->name('reservation_thank_you');
Route::get( 'reservations/{confirm_id}', 'ReservationController@getConfirmPage')->name('reservation_confirm');
Route::post('reservations/{confirm_id}', 'ReservationController@getConfirmPage');

Route::get('admin',              'AdminController@getDashboard');
Route::get('admin/settings',     'AdminController@getSettingsDashboard');
Route::get('admin/reservations', 'AdminController@getReservationDashboard');
/**
 * Handle control on Session
 */
Route::post('sessions', 'SessionController@update');
Route::post('outlet-reservation-settings', 'OutletReservationSettingController@update');




Route::get('test', function(App\Http\Controllers\BookingController $c, App\Http\Controllers\AdminController $a, App\Http\Controllers\SessionController $s){

    //return \App\Timing::hasNewUpdate()->get()->count();

    //return view('reservations.booking-summary');

    //return view('reservations.booking-form-2');
//    return Carbon\Carbon::now(App\OutletReservationSetting::timezone());

//    $outlet_id = 2;
//    event(new \App\Events\ShouldUpdateCacheDatesWithAvailableTimeEvent($outlet_id));
//
//    echo "has dispatch should update cache > query should recalculate";
//
//    return redirect('booking-form');

//    return \App\Reservation::validGroupByDateTimeCapacity();
//    return $c->availableTime();
//    dd($c->reservation_pax_size);

//    dd(App\OutletReservationSetting::bufferConfigAsMap());
//    dd(App\OutletReservationSetting::brandId());

    /**
     * Setter on reservation CANNOT mutate on attribute X
     * when reservation_info not pass attribute X
     *
     * Getter otherwise can
     */
//    $r = new \App\Reservation();
//    $r->save();

    //dd(App\OutletReservationSetting::allConfigByGroup());
//    $notification_config = App\OutletReservationSetting::notificationConfig();
//    dd($notification_config('HOURS_BEFORE_RESERVATION_TIME_TO_SEND_SMS'));

    /** @var \App\Reservation $r */
//    $r = App\Reservation::first();
//    dd($r);
//    $confirm_id = $r->confirm_id;
//    dd($r->confirm_coming_url);
    //return redirect()->route('reservation_confirm', compact('confirm_id'));
    //dd($c->buildDatesWithAvailableTime());

//    $t = App\Timing::all();
//    dd($t);
    //return view('layouts.app');

    //dd($c->availableTime());

//    $r = App\Reservation::first();
//    //dd($r);
//    return $c->apiResponse($r->toArray());
//    $req =  new \App\Http\Requests\ApiRequest();
//    dd($a->getSettingsDashboard($req));
//    return (string)url('/');

    return $s->update();
});

