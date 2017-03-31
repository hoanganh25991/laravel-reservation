<?php

namespace App\Console;

use Illuminate\Contracts\Logging\Log;
use Psr\Log\LoggerInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;

class HoiKernel {
    protected $app;
    protected $events;
    protected $bootstrappers = [
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        \Illuminate\Foundation\Bootstrap\SetRequestForConsole::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ];

    public function __construct(Application $app, Dispatcher $events){
        $this->app = $app;
        $this->events = $events;
    }

    public function bootstrap() {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }

        // If we are calling an arbitrary command from within the application, we'll load
        // all of the available deferred providers which will make all of the commands
        // available to an application. Otherwise the command will not be available.
        $this->app->loadDeferredProviders();
    }

    public function bootstrappers(){
        return $this->bootstrappers;
    }
    
    public function handle($callback){
        $this->bootstrap();
        $callback();
    }
    
    public function terminate(){
        $this->app->terminate();
    }
}