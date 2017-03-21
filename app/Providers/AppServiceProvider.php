<?php

namespace App\Providers;

use App\Jobs\HoiJobs;
use Illuminate\Support\ServiceProvider;

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
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(){
    }
}
