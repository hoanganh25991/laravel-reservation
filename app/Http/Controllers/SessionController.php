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
        $data = json_decode($req->getContent(), JSON_NUMERIC_CHECK);
        $action_type = $data['type'];

        switch($action_type){
            case Call::AJAX_UPDATE_WEEKLY_SESSIONS:
                $weekly_sessions = $data['weekly_sessions'];

                $deleted_sessions = $data['deleted_sessions'];
                
                

                try{
                    /**
                     * Update
                     */
                    foreach($weekly_sessions as $session_data){
                        $timings_data = $session_data['timings'];
                        unset($session_data['timings']);

                        $s = Session::findOrNew($session_data['id']);
                        $s->fill( Session::sanityData($session_data));
                        $s->save();

                        foreach($timings_data as $timing_data){
                            $timing = Timing::findOrNew($timing_data['id']);
                            $timing->session_id = $s->id;
                            $timing->fill(Timing::sanityData($timing_data));
                            $timing->save();
                        }
                    }

                    /**
                     * Delete
                     */
                    foreach($deleted_sessions as $session_data){
                        $s = Session::find($session_data['id']);

                        //When this session & it timing not exist before
                        //ignore them
                        if(is_null($s)){
                            continue;
                        }


                        $s->delete();

                        $timings_data = $session_data['timings'];

                        foreach($timings_data as $timing_data){
                            $timing = Timing::find($timing_data['id']);

                            if(is_null($timing)){
                                continue;
                            }

                            $timing->delete();
                        }

                    }


                    $data = [];
                    $code = 200;
                    $msg = Call::AJAX_UPDATE_WEEKLY_SESSIONS_SUCCESS;
                }catch(\Exception $e){
                    $data = $e->getMessage();
                    $code = 200;
                    $msg = Call::AJAX_UPDATE_WEEKLY_SESSIONS_ERROR;
                }
                break;
            case Call::AJAX_DELETE_WEEKLY_SESSIONS:
                
            default:
                $data = $req->all();
                $code = 200;
                $msg = Call::AJAX_UNKNOWN_CASE;
                break;
        }


        return $this->apiResponse($data, $code, $msg);
    }
}
