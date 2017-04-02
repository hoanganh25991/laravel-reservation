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
         * View composer for Admin page
         * Admin page need outlet to switch between in navigator
         */
        View::composer(['admin.navigator'], function ($view) {
            $outlets = collect([]);
            /** @var ReservationUser $user */
            $user = Auth::user();

            if(!is_null($user) && $user->canAccessAdminPage()){
                $outlets = Outlet::whereIn('id', $user->allowedOutletIds())->get();
            }
            
            $view->with(compact('outlets'));            
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
