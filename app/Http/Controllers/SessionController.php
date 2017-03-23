<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\Session;
use App\Timing;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\HoiAjaxCall as Call;
use Illuminate\Support\Facades\DB;

class SessionController extends HoiController{

    use ApiResponse;

    public function update(ApiRequest $req){
        $action_type = $req->get('type');

        switch($action_type){
            case Call::AJAX_UPDATE_WEEKLY_SESSIONS:
                $weekly_sessions = $req->get('data');

                $t = new Timing();
                $s = new Session();

                foreach($weekly_sessions as $session_arr){
                    if(!isset($session_arr['timings'])){
                        $session_arr['timings'] = [];
                    }

                    $timings = $session_arr['timings'];

                    foreach($timings as $timing_arr){
                        $timing_arr = $t->sanityData($timing_arr);

                        DB::table('timing')
                            ->where('id', $timing_arr['id'])
                            ->update($timing_arr);
                    }

                    unset($session_arr['timings']);
                    $session_arr = $s->sanityData($session_arr);
                    DB::table('session')
                        ->where('id', $session_arr['id'])
                        ->update($session_arr);
                }


//                $sessions = $weekly_sessions->map(function($session_array) use($timings_collection){
//                    $session = new Session($session_array);
//
////                    $timings = $session_array['timings'];
////                    $timings_collection->push($timings);
//                    return $session;
//                });
//
//                $timings_collection = $timings_collection->collapse();
//
//                $timings = $timings_collection->map(function($timing_array){
//                    $timing = new Timing($timing_array);
//                    return $timing;
//                });





                break;

            default:
                break;
        }


        return $this->apiResponse('hello world', 200, 'vkl');
    }
}
