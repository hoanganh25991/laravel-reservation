<?php

namespace App\Http\Middleware;

use Closure;
use App\ReservationUser;
use App\Traits\NeedJsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;

class Administrator {

    use NeedJsonResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next){
        /** @var ReservationUser $user */
        $user = Auth::user();

        if(!is_null($user) && $user->isAdministrator()){
            return $next($request);
        }

        if($this->needJsonResponse($request)){
            throw new AuthenticationException("Need admin role to go to page");
        }else{
            return redirect()->route('login');
        }
    }
}
