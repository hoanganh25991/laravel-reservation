<?php
namespace App\Traits;

use Response;
use Illuminate\Database\Eloquent\Collection;

define("WARNING", "we still not handle this situation");

trait ApiResponse{
    public function apiResponse($data, $statusCode = 200, $statusMsg = "success"){
        /** change format of response */
        /** if 200, only return data */
        if($statusCode == 200){
//            if(get_class($data) == Collection::class){
//                dd($data);
//            }
            
            
            
            return Response::json($data)->setEncodingOptions(JSON_NUMERIC_CHECK);
        }

        /** for error situation, return code|msg|data */
        return Response::json([
            'statusCode' => $statusCode,
            'statusMsg' => $statusMsg,
            'data' => $data
        ], $statusCode)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
