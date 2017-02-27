<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Timing extends Model
{
    protected $table = 'timing';

    protected function order($query){
        $query->orderBy('first_arrival_time', 'asc');
    }

    public function scopeOrder($query){
        $this->order($query);
    }
}
