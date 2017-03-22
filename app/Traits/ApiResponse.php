<?php
namespace App\Traits;

use Response;
use Illuminate\Database\Eloquent\Collection;

define("WARNING", "we still not handle this situation");

trait ApiResponse{
    public function apiResponse($data, $statusCode = null, $statusMsg = "success"){
        /**
         * Call only with $data, consider as $statusCode == 200
         */
        if(is_null($statusCode)){
            return Response::json($data)->setEncodingOptions(JSON_NUMERIC_CHECK);
        }

        /** for other situations, return code|msg|data */
        return Response::json([
            'statusCode' => $statusCode,
            'statusMsg' => $statusMsg,
            'data' => $data
        ], $statusCode)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
