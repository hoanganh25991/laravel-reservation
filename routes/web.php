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
/**
 * Routes for Auth
 */
Auth::routes();
Route::get('logout', function (){
    Auth::logout();
//    return redirect()->back();
    return redirect('');
});



/**
 * Routes for Booking
 */
Route::get('', 'BookingController@getBookingForm');
Route::post('', 'BookingController@getBookingForm');

Route::get('home', 'BookingController@getBookingForm');
Route::post('home', 'BookingController@getBookingForm');

Route::get('reservations/thank-you', 'ReservationController@getThankYouPage')->name('reservation_thank_you');
Route::get('reservations/{confirm_id}', 'ReservationController@getConfirmPage')->name('reservation_confirm');
Route::post('reservations/{confirm_id}', 'ReservationController@getConfirmPage');

/**
 * Routes for Admin page
 */
Route::group([
    'middleware' => 'staff',
    'prefix' => 'admin'
], function (){
    Route::get('', 'AdminController@getDashboard')->name('admin');
    Route::post('', 'AdminController@setUpOuletId');

    Route::group(['middleware' => 'reservations'], function (){
        Route::get('reservations', 'AdminController@getReservationDashboard');
        Route::post('reservations', 'AdminController@getReservationDashboard');
    });

    Route::group(['middleware' => 'administrator'], function (){
        Route::get('settings', 'AdminController@getSettingsDashboard');
        Route::post('settings', 'AdminController@getSettingsDashboard');
    });
});


/**
 * Handle update post from admin page
 */
Route::group(['middleware' => 'administrator'], function (){
    Route::post('sessions', 'SessionController@update');
    Route::post('outlet-reservation-settings', 'OutletReservationSettingController@update');
});

Route::group(['middleware' => 'staff'], function (){
    Route::post('reservations', 'ReservationController@update');
});


/**
 * Group for api call
 */
Route::group([
    'prefix' => 'api',
    'middleware' => 'api'
], function (){
    /**
     * Route to book reservation
     */
    Route::get('', 'BookingController@getBookingForm');
    Route::post('', 'BookingController@getBookingForm');

    Route::get('home', 'BookingController@getBookingForm');
    Route::post('home', 'BookingController@getBookingForm');

    //Route::get('reservations/thank-you', 'ReservationController@getThankYouPage')->name('reservation_thank_you');
    //Route::get('reservations/{confirm_id}', 'ReservationController@getConfirmPage')->name('reservation_confirm');
    //Route::post('reservations/{confirm_id}', 'ReservationController@getConfirmPage');

    Route::get('outlets', 'OutletController@fetchAllOutlet');
    Route::post('outlets', 'OutletController@fetchAllOutlet');

    /**
     * Route to admin page
     */
    Route::group([
        'middleware' => 'staff',
        'prefix' => 'admin'
    ], function (){
        //bring admin out of
        Route::get('', 'AdminController@getDashboard')->name('admin');
        Route::post('', 'AdminController@setUpOuletId');

        Route::group(['middleware' => 'reservations'], function (){
            Route::get('reservations', 'AdminController@getReservationDashboard');
            Route::post('reservations', 'AdminController@getReservationDashboard');
        });

        Route::group(['middleware' => 'administrator'], function (){
            Route::get('settings', 'AdminController@getSettingsDashboard');
            Route::post('settings', 'AdminController@getSettingsDashboard');
        });
    });


    /**
     * Handle update post from admin page
     */
    Route::group(['middleware' => 'administrator'], function (){
        Route::post('sessions', 'SessionController@update');
        Route::post('outlet-reservation-settings', 'OutletReservationSettingController@update');
    });

    Route::group(['middleware' => 'staff'], function (){
        Route::post('reservations', 'ReservationController@update');
    });
});


Route::post('paypal', 'PayPalController@handlePayment');


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

    $hour_before =
        \Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', '2017-04-04 08:00:00'),
            false);
    return $hour_before;
});

/**
 * Fix explicit tell which brand_id used
 */
Route::get('{brand_id}/{go?}', function($brand_id, $go){
    return compact('brand_id', 'go');
});