<?php
namespace App\Traits;

trait HoiAuth {
    /**
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function redirectTo(){
        return route('admin');
    }
}