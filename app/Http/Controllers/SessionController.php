<?php

namespace App\Http\Controllers;

use App\Session;
use App\Timing;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use Illuminate\Support\Facades\DB;
use App\Libraries\HoiAjaxCall as Call;

class SessionController extends HoiController{

    use ApiResponse;

    public function update(ApiRequest $req){
        $action_type = $req->get('type');

        switch($action_type){
            case Call::AJAX_UPDATE_WEEKLY_SESSIONS:
                $weekly_sessions = $req->get('data');

                /**
                 * Resue Timing, Session obj to call sanityDate
                 * On data_arr
                 */
                $t = new Timing();
                $s = new Session();

                foreach($weekly_sessions as $session_arr){
                    /**
                     * Through serialize, timings as empty array lose
                     * When encode & decode, reassign as default []
                     */
                    if(!isset($session_arr['timings'])){
                        $session_arr['timings'] = [];
                    }

                    $timings = $session_arr['timings'];

                    foreach($timings as $timing_arr){
                        //update
                        $timing_arr = $t->sanityData($timing_arr);
                        DB::table('timing')->where('id', $timing_arr['id'])->update($timing_arr);
                    }

                    /**
                     * @warn Update in to DB can't understand relation
                     * sanityData also not check this case
                     */
                    unset($session_arr['timings']);
                    //update
                    $session_arr = $s->sanityData($session_arr);
                    DB::table('session')->where('id', $session_arr['id'])->update($session_arr);
                }


                $data = [];
                $code = 200;
                $msg = Call::AJAX_UPDATE_WEEKLY_SESSIONS_SUCCESS;
                break;

            default:
                $data = $req->all();
                $code = 200;
                $msg = Call::AJAX_UNKNOWN_CASE;
                break;
        }


        return $this->apiResponse($data, $code, $msg);
    }
}
