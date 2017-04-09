<?php

namespace App\Http\Middleware;

use Closure;
use App\ReservationUser;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use App\OutletReservationSetting as Setting;


class ResolveBrandOutletId {
    
    const URI_HAS_BRAND_ID = 'URI_HAS_BRAND_ID';
    const USER_IN_ADMIN_PAGE = 'USER_IN_ADMIN_PAGE';
    
    protected $request;

    /**
     * Handle an incoming request.
     *
     * @param Route $route
     * @param Request $req
     * @internal param \Illuminate\Http\Request $request
     * @internal param Closure $next
     */
    public function __construct(Route $route, Request $req){
        $this->request = $req;
    }

    public function handle($request, Closure $next){
        /**
         * Try to resolve brand_id
         */
        //brand id from route
        $brand_id = $this->request->route()->parameter('brand_id');

        if(!is_null($brand_id)){
            Setting::injectBrandId($brand_id);
        }else{
            /** @var ReservationUser $user */
            $user = Auth::user();
            if(!is_null($user)){
                $user->injectBrandId();
            }
        }

        /**
         * Try to resolve outlet_id
         */
        $outlet_id = $this->request->get('outlet_id') ?: $this->request->json('outlet_id');
        
        if(!is_null($outlet_id)){
            Setting::injectOutletId($outlet_id);
        }

        return $next($request);
    }
}
