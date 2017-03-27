<?php

namespace App\Providers;

use App\Timing;
use Carbon\Carbon;
use App\Jobs\HoiJobs;
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
         * Interval run HoiJobs
         * @fail bcs, each request dispatch job????
         * @see "queue-jobs.php"
         */
        //dispatch(new HoiJobs);

        /**
         * Add custom validator on Timing
         */
        Validator::extend('arrival_time',  function ($attribute, $value, $parameters, $validator) {
            return Timing::validateArrivalTime($value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(){
    }
}
