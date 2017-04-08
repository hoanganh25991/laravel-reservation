<?php

namespace App\Http\Middleware;

use Closure;
use App\ReservationUser;
use Illuminate\Support\Facades\Auth;
use App\OutletReservationSetting as Setting;

class Administrator {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next){
        /** @var ReservationUser $user */
        $user = Auth::user();

        if(is_null($user)){
            return redirect('login');
        }

        if($user->isAdministrator()){
            return $next($request);
        }

        return redirect()->route('admin');
    }
}
