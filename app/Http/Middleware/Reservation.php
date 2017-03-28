<?php

namespace App\Http\Middleware;

use Closure;
use App\ReservationUser;
use Illuminate\Support\Facades\Auth;

class Reservation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        /** @var ReservationUser $user */
        $user = Auth::user();

        if(is_null($user)){
            return redirect('login');
        }

        if(!$user->isReservations()){
            return redirect()->route('admin');
        }

        return $next($request);
    }
}
