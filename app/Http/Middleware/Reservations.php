<?php

namespace App\Http\Middleware;

use Closure;
use App\ReservationUser;
use Illuminate\Support\Facades\Auth;
use App\OutletReservationSetting as Setting;

class Reservations
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
        
        if($user->isAdministrator()){
            return $next($request);
        }
        

        if($user->isReservations()){
            $brand_id = $user->brand_id;
            if(is_null($brand_id)){
                throw new \Exception('User not assigned brand_id, can not determine allowed him move on or not');
            }
            //Have to inject
            Setting::injectBrandId($brand_id);

            return $next($request);
        }

        return redirect()->route('admin');

    }
}
