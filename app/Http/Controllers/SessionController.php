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
        $action_type = $req->json('type');

        switch($action_type){
            case Call::AJAX_UPDATE_SESSIONS:
                $sessions = $req->json('sessions');

                $deleted_sessions = $req->json('deleted_sessions');
                
                $deleted_timings  = $req->json('deleted_timings');

                $validator = null;

                try{
                    /**
                     * Update
                     */
                    foreach($sessions as $session_data){
                        $validator = Session::validateOnCRUD($session_data);
                        
                        if($validator->fails()){
                            throw new \Exception(Call::AJAX_VALIDATE_FAIL);
                        }
                        
                        $s = Session::findOrNew($session_data['id']);
                        $s->fill($session_data);
                        $s->save();

                        $timings_data = $session_data['timings'];
                        foreach($timings_data as $timing_data){

                            $validator = Timing::validateOnCRUD($timing_data);

                            if($validator->fails()){
                                throw new \Exception(Call::AJAX_VALIDATE_FAIL);
                            }

                            $timing = Timing::findOrNew($timing_data['id']);
                            $timing->fill($timing_data);
                            $timing->session_id = $s->id;
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

                    foreach($deleted_timings as $timing_data){
                        $timing = Timing::find($timing_data['id']);

                        if(is_null($timing)){
                            continue;
                        }

                        $timing->delete();
                    }


                    $data = [
                        'weekly_sessions' => $this->fetchUpdatedWeeklySessions(),
                        'special_sessions'=> $this->fetchUpdatedSpecialSessions()
                    ];
                    $code = 200;
                    $msg = Call::AJAX_SUCCESS;
                }catch(\Exception $e){
                   if($e->getMessage() == Call::AJAX_VALIDATE_FAIL){
                       $data = $validator->getMessageBag()->toArray();
                       $code = 200;
                       $msg  = Call::AJAX_VALIDATE_FAIL;
                   }else{
                       $data = $e->getMessage();
                       $code = 200;
                       $msg = Call::AJAX_ERROR;
                   }
                }
                break;
            default:
                $data = $req->all();
                $code = 200;
                $msg = Call::AJAX_UNKNOWN_CASE;
                break;
        }


        return $this->apiResponse($data, $code, $msg);
    }

    public function fetchUpdatedWeeklySessions(){
        $weekly_sessions = Session::normalSession()->with('timings')->get();

        return $weekly_sessions;
    }
    
    public function fetchUpdatedSpecialSessions(){
        $special_sesssions = Session::allSpecialSession()->with('timings')->get();
        
        return $special_sesssions;
    }
}
