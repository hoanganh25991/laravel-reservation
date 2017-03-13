<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HoiController extends Controller
{
    public function __get($field){
        $method   = 'get'.Str::studly($field).'Attribute';
        if(method_exists($this, $method))
            return $this->$method();

        return $this->$field;
    }
    
    public function __set($field, $value){
        $method   = 'set'.Str::studly($field).'Attribute';
        if(method_exists($this, $method))
            return $this->$method($value);

        $this->$field = $value;
    }

}
