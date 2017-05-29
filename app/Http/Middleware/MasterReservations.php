<?php

namespace App\Http\Middleware;

use App\ReservationUser;
use Closure;

class MasterReservations
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

        if($user->isReservations()){
            return $next($request);
        }

        return redirect()->route('admin');
    }
}
