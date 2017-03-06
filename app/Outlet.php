<?php

namespace App;

/**
 * @property mixed outlet_name
 */
class Outlet extends HoiModel {
    
    protected $table = 'outlet';
    
    public function getNameAttribute(){
        return $this->outlet_name;
    }
}
