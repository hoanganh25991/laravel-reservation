<?php

namespace App\Providers;

use App\Outlet;
use App\Timing;
use App\ReservationUser;
use App\Libraries\HoiHashPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\OutletReservationSetting as Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){
        /**
         * Add HoiHelpers function
         * In global
         */
        $hoi_helpers_path = app_path('Libraries/HoiHelpers.php');
        /** @noinspection PhpIncludeInspection */
        require_once($hoi_helpers_path);
        
        /**
         * Add custom validator on Timing
         */
        Validator::extend('arrival_time',  function ($attribute, $value, $parameters, $validator) {
            return Timing::validateArrivalTime($value);
        });

        /**
         * Add custom validator on outlet_id
         */
//        Validator::extend('handled_outlet_id',  function ($attribute, $value, $parameters, $validator) {
//            return Outlet::validateHandledOutletId($value);
//        });

        /**
         * View composer for Admin page
         * Admin page need outlet to switch between in navigator
         */
        View::composer(['admin.navigator'], function ($view) {
            $outlets   = collect([]);
            /** @var ReservationUser $user */
            $user = Auth::user();

            if(!is_null($user) && $user->canAccessAdminPage()){
                $outlets = $user->outletsCanAccess();
            }
            
            $navigator_state = [
                'outlets'   => $outlets,
                'base_url'  => url('admin'),
            ];
            
            $view->with(compact('navigator_state'));
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(){
        $this->app->singleton('hash', function () {
            return new HoiHashPassword();
        });
        
        
    }
}
