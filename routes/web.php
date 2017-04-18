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
use App\OutletReservationSetting as Setting;



/**
 * Routes for Auth
 */
Route::any('login', 'Auth\LoginController@hoiLogin')->name('login');
Route::get('logout', 'Auth\LoginController@hoiLogout')->name('logout');
//Route::post('login', 'Auth\LoginController@login');

/**
* Auto inject brand id, outlet id
* @see \App\Http\Middleware\ResolveBrandOutletId
*/

/**
 * Handle booking
 */
Route::any('{brand_id}', 'BookingController@getBookingForm')->where('brand_id', '[0-9]+');
Route::get('reservations/thank-you', 'ReservationController@getThankYouPage')->name('reservation_thank_you');
Route::any('reservations/{confirm_id}', 'ReservationController@getConfirmPage')->name('reservation_confirm');
/**
 * Handle paypal
 */
Route::any('{brand_id}/paypal', 'PayPalController@handlePayment')->where('brand_id', '[0-9]+');
//Paypal now belongs to outlet config
//Don't need to sub under brand_id
Route::any('paypal', 'PayPalController@handlePayment')->where('brand_id', '[0-9]+');

/**
 * Handle admin page
 * Need permisstion
 */
//staff user handle reservations
Route::group(['middleware' => 'reservations'], function (){
    Route::any('admin', 'AdminController@getDashboard')->name('admin');
    Route::any('admin/reservations', 'AdminController@getReservationDashboard');
});

//administartor detail
Route::group(['middleware' => 'administrator'], function (){
    Route::any('admin/settings', 'AdminController@getSettingsDashboard');
});


/**
 * Api call
 * Currently only support frontend call
 */
Route::group(['prefix' => 'api','middleware' => 'api'], function (){
    Setting::injectBrandId(1);
    Route::any('', 'BookingController@getBookingForm');
    Route::any('outlets', 'OutletController@fetchAllOutlet');
});




Route::get('test', function (App\Http\Controllers\BookingController $c, App\Http\Controllers\AdminController $a,
    App\Http\Controllers\SessionController $s, \App\Http\Requests\ApiRequest $req){

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

//    return $s->update();


//    $r = new App\Reservation();
//    $r->reservation_timestamp = "2017-03-05 00:00:00";
//
//    $r->save();
//
//    $b = $r->send_confirmation_by_timestamp;
//
//    dd($r, $b);
//    $hoi_jobs = new \App\Jobs\HoiJobs();
//
//    $hoi_jobs->handle();

//    return url('');

    //return $req->url();

    //App\OutletReservationSetting::allConfigByGroup();

    //$b = App\OutletReservationSetting::bufferConfig();

//    $hour_before =
//        \Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', '2017-04-04 08:00:00'),
//            false);
//    return $hour_before;

    //$r = App\Reservation::find(117);

    //return $r->confirm_coming_url;
    //return $r->toJson();

    App\OutletReservationSetting::injectBrandId(1);
    $setting = App\Outlet::find(2);
    return $setting->toJson();
});