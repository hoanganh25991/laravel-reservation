<?php

namespace App\Http\Middleware;

use Closure;
use App\ReservationUser;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use App\OutletReservationSetting as Setting;


class ResolveBrandOutletId {

    /** @var Request $req */
    protected $req;

    /**
     * Handle an incoming request.
     *
     * @param Route $route
     * @param Request $req
     * @internal param \Illuminate\Http\Request $request
     * @internal param Closure $next
     */
    public function __construct(Route $route, Request $req){
        $this->req = $req;
    }

    public function handle($request, Closure $next){
        $req = $this->req;
        /**
         * Resolve for normal case from customer
         */
        // Try to resolve brand_id
        $brand_id = $this->tryGetFromAllTypeOfRequest('brand_id');

        if(!is_null($brand_id)){
            Setting::injectBrandId($brand_id);
        }

        // Try to resolve outlet_id
        $outlet_id = $this->tryGetFromAllTypeOfRequest('outlet_id');
        
        if(!is_null($outlet_id)){
            Setting::injectOutletId($outlet_id);
        }

        /**
         * Resolve for staff login to manage admin
         */
        if($req->is('admin*')){
            /** @var ReservationUser $user */
            $user = Auth::user();

            if(!is_null($user)){
                $user->injectBrandId();
            }
        }

        return $next($request);
    }

    public function tryGetFromAllTypeOfRequest($key){
        $req     = $this->req;
        $methods = ['get', 'json', 'route'];

        // Init while loop
        $value = null;
        $index = 0;

        while(is_null($value) && $index < count($methods)){

            $method = $methods[$index];
            $value  = $req->$method($key);
            $index++;
        }

        return $value;
    }

}
