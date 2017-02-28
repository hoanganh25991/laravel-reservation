<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\OutletReservationSetting as Setting;

class Timing extends Model
{
    const INTERVAL_STEPS = [15, 30, 60];
    const ARRIVAL_STEPS  = [15];

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

        $start_time = Carbon::createFromFormat('H:i:s', $start, Setting::TIME_ZONE);
        $end_time   = Carbon::createFromFormat('H:i:s', $end, Setting::TIME_ZONE);

        $count = 0;
        $this->chunk = collect([]);
        while($start_time->lt($end_time)){
            $options = [
                $start_time->format('H:i') => [
                    'capacity_1' => $this->capacity_1,
                    'capacity_2' => $this->capacity_2,
                    'capacity_3_4' => $this->capacity_3_4,
                    'capacity_5_6' => $this->capacity_5_6,
                    'capacity_7_x' => $this->capacity_7_x
                ]
            ];
            $this->chunk->push($options);
            $count++;
            $start_time->addMinutes($interval);
        }
        //$this->chunk = 'fuck';
    }
    
    
    public function chunk(){
        /**
         * Chunk by the minimal
         * uoc so chung nho nhat
         */
    }
    
    
}
