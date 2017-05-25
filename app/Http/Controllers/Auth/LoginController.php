<?php

namespace App\Http\Controllers\Auth;

use App\Traits\HoiAuth;
use App\ReservationUser;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Libraries\HoiAjaxCall as Call;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller {

    use HoiAuth;
    use ApiResponse;
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     */
    public function __construct(){ }

    /**
     * Get the login username to be used by the controller.
     * @return string
     */
    public function username(){
        return 'user_name';
    }

    public function hoiLogin(ApiRequest $req){
        if($req->method() == 'POST'){
            return $this->login($req);
        }

        return $this->showLoginForm();
    }

    /**
     * Log the user out of the application.
     * @return \Illuminate\Http\Response
     */
    public function hoiLogout(){
        Auth::logout();

        return redirect()->route('login');
    }
    
    public function apiLogin(ApiRequest $req){
        
        if($req->method() == 'GET')
            throw new \Exception('Sorry. apiLogin not support get');
        
        // Handle POST case
        $action_type = $req->json('type');

        switch($action_type){
            case Call::AJAX_LOGIN:
                $user_name = $req->json('user_name');
                $password = $req->json('password');

                //$user = ReservationUser::where('username', $username);

                $logined = Auth::attempt([
                    'user_name' => $user_name,
                    'password'  => $password
                ], true);

                if($logined){
                    /** @var ReservationUser $user */
                    $user = Auth::user();
                    $data = compact('user');
                    $code = 200;
                    $msg  = Call::AJAX_LOGIN_SUCCESS;
                    
                    return $this->apiResponse($data, $code, $msg);
                }

                /**
                 * Fail to login
                 */
                $data = [];
                $code = 422;
                $msg  = Call::AJAX_LOGIN_FAIL;
                
                return $this->apiResponse($data, $code, $msg);

            case Call::AJAX_LOGOUT:
                Auth::logout();
                $data = [];
                $code = 200;
                $msg  = Call::AJAX_LOGOUT_SUCCESS;

                return $this->apiResponse($data, $code, $msg);

            default:
                break;
        }

        $data = [];
        $code = 422;
        $msg  = Call::AJAX_UNKNOWN_CASE;
        return $this->apiResponse($data, $code, $msg);
    }

}
