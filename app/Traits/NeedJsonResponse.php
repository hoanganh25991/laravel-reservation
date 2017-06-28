<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait NeedJsonResponse{
    /**
     * @param Request $request
     * @return mixed
     */
    public function needJsonResponse($request) {
//        $fromApiGroup = preg_match('/api/', $request->url());
//        $isPostMethod = $request->method() == 'POST';
//        $isAjax       = $request->ajax();
//        
//        return $fromApiGroup || $isPostMethod || $isAjax;
        return $request->expectsJson();
    }
}
