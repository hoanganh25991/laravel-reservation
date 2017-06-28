<?php
namespace App\Traits;

trait NeedJsonResponse{
    public function needJsonResponse($request) {
        $fromApiGroup = preg_match('/api/', $request->url());
        $isPostMethod = $request->method() == 'POST';
        $isAjax       = $request->ajax();
        
        return $fromApiGroup || $isPostMethod || $isAjax;
    }
}
