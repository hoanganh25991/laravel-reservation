<?php

namespace App\Http\Middleware;

use App\ReservationUser;
use Closure;
use Illuminate\Support\Facades\Auth;

class Staff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        /** @var ReservationUser $user */
        $user = Auth::user();
        
        if(is_null($user)){
            return redirect('login');
        }
        
        if(!$user->canAccessAdminPage()){
            return redirect()->back();
        }
        
        return $next($request);
    }
}
