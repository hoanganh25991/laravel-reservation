<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){
        $hoi_helpers_path = app_path('Libraries/HoiHelpers.php');
        /** @noinspection PhpIncludeInspection */
        require_once($hoi_helpers_path);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
