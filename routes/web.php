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

use Illuminate\Support\Facades\Route;

Route::get('/', function(){return view('index');});
Route::get('/home', function(){return 'fuck';});
Route::get('/fuck', function(){return 'fuck you';});

Auth::routes();

//var_dump(auth());
Route::get('test', function(){
    return ['name' => 'Anh Le Hoang'];
});

Route::post('booking/date-available', 'BookingController@dateAvailable');

Route::get('test/session-scope', function(){
//    $s = App\Session::specialSession()->get();
//    $s = App\Session::normalSession()->get();
    $s = App\Session::available();
    return $s;
});
