<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\OutletReservationSetting as Setting;

class Timing extends Model
{
    protected $table = 'timing';

    protected function order($query){
        $query->orderBy('first_arrival_time', 'asc');
    }

    public function scopeOrder($query){
        $this->order($query);
    }

    public function chunkByInterval(){
        $start = $this->first_arrival_time;
        $end = $this->last_arrival_time;
        $interval = $this->interval_minutes;

        $start_time = Carbon::createFromFormat('H:i:s', $start)->timezone(Setting::TIME_ZONE);
        $end_time = Carbon::createFromFormat('H:i:s', $end)->timezone(Setting::TIME_ZONE);

        $count = 0;
        $this->chunk = collect([]);
        while($start_time->lt($end_time)){
            $this->chunk->push($start_time->format('H:i'));
            $count++;
            $start_time->addMinutes($interval);
        }
        //$this->chunk = 'fuck';
    }
}
