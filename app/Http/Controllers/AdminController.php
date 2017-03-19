<?php

namespace App\Http\Controllers;

class AdminController extends HoiController {

    public function getDashboard(){
        return view('admin.index');
    }
}
