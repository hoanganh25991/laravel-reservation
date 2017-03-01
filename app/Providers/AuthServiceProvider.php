<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\AuthServiceProvider as LaravelAuthServiceProvider;
use Illuminate\Auth\DatabaseUserProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
//class AuthServiceProvider extends LaravelAuthServiceProvider {
//    protected function createDatabaseProvider($config)
//    {
//        $connection = $this->app['db']->connection();
//
//        $database = new DatabaseUserProvider($connection, $this->app['hash'], $config['table']);
//
//        /**
//         * Retrieve a user by their unique identifier and "remember me" token.
//         *
//         * @param  mixed  $identifier
//         * @param  string  $token
//         * @return \Illuminate\Contracts\Auth\Authenticatable|null
//         */
//        $database->retrieveByToken = function($identifier, $token){
//            $user = $this->conn->table($this->table)
//                ->where('id', $identifier)
//                ->where('remember_token', $token)
//                ->first();
//
//            return $this->getGenericUser($user);
//        };
//
//        return $database;
//    }
//}