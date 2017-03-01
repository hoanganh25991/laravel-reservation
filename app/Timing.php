<?php

namespace App;

use Carbon\Carbon;
use App\Helpers\GCD;
use App\Helpers\GreatestCommonFactor;
use Illuminate\Database\Eloquent\Model;
use App\OutletReservationSetting as Setting;

/**
 * @property mixed first_arrival_time
 * @property mixed interval_minutes
 * @property mixed capacity_1
 * @property mixed last_arrival_time
 * @property mixed capacity_2
 * @property mixed capacity_3_4
 * @property mixed capacity_5_6
 * @property mixed type
 * @property Session session
 */
class Timing extends Model
{
//    const INTERVAL_STEPS = [15, 21, 30, 60];
    const INTERVAL_STEPS = [15, 20, 30, 60];
    const ARRIVAL_STEPS  = [15];

    protected $table = 'timing';
    
    protected function order($query){
        $query->orderBy('first_arrival_time', 'asc');
    }

    public function scopeOrder($query){
        $this->order($query);
    }
    
    public function session(){
        return $this->hasOne(Session::class, 'id', 'session_id');
    }
    
    public function assignSession(Session $session){
        $this->session = $session;
    }

    public function chunkByInterval(){
        $start = $this->first_arrival_time;
        $end = $this->last_arrival_time;
//        $interval = $this->interval_minutes;
        $allow_steps = array_merge(self::INTERVAL_STEPS, self::ARRIVAL_STEPS);
        $interval = GCD::find($allow_steps);
//        $interval = (new GreatestCommonFactor(self::INTERVAL_STEPS))->calculate();

        $start_time = Carbon::createFromFormat('H:i:s', $start, Setting::TIME_ZONE);
        $end_time   = Carbon::createFromFormat('H:i:s', $end, Setting::TIME_ZONE);

        $count = 0;
        $this->chunk = collect([]);
        while($start_time->lt($end_time)){
//            $options = [
//                $start_time->format('H:i') => [
//                    'capacity_1' => $this->capacity_1,
//                    'capacity_2' => $this->capacity_2,
//                    'capacity_3_4' => $this->capacity_3_4,
//                    'capacity_5_6' => $this->capacity_5_6,
//                    'capacity_7_x' => $this->capacity_7_x,
//                    'type'        => $this->type
//                ]
//            ];
            $options = (object)[
                    'time' => $start_time->format('H:i'),
                    'first_arrival_time' => $this->first_arrival_time,
                    'interval_minutes' => $this->interval_minutes,
                    'capacity_1' => $this->capacity_1,
                    'capacity_2' => $this->capacity_2,
                    'capacity_3_4' => $this->capacity_3_4,
                    'capacity_5_6' => $this->capacity_5_6,
                    'type'        => $this->session->type
            ];
//            $options->getMinutes = function(){
//                //13:00:00 > 13, 00
//                $timeInfo = explode(":", $this->time);
//                $hour = (int)$timeInfo[0];
//                $minute = (int)$timeInfo[1];
//
//                return $hour * 60 + $minute;
//            };
            
            $this->chunk->push($options);
            $count++;
            $start_time->addMinutes($interval);
        }
        //$this->chunk = 'fuck';

        return $this->chunk;
    }
    
    
    public function chunk(){
        /**
         * Chunk by the minimal
         * uoc so chung nho nhat
         */
    }

    public function getChunkAttribute(){
        $start = $this->first_arrival_time;
        $end = $this->last_arrival_time;
//        $interval = $this->interval_minutes;
        $allow_steps = array_merge(self::INTERVAL_STEPS, self::ARRIVAL_STEPS);
        $interval = GCD::find($allow_steps);
//        $interval = (new GreatestCommonFactor(self::INTERVAL_STEPS))->calculate();

        $start_time = Carbon::createFromFormat('H:i:s', $start, Setting::TIME_ZONE);
        $end_time   = Carbon::createFromFormat('H:i:s', $end, Setting::TIME_ZONE);

        $count = 0;
        $chunk = collect([]);
        while($start_time->lt($end_time)){
//            $options = [
//                $start_time->format('H:i') => [
//                    'capacity_1' => $this->capacity_1,
//                    'capacity_2' => $this->capacity_2,
//                    'capacity_3_4' => $this->capacity_3_4,
//                    'capacity_5_6' => $this->capacity_5_6,
//                    'capacity_7_x' => $this->capacity_7_x,
//                    'type'        => $this->type
//                ]
//            ];
            $options = (object)[
                'time' => $start_time->format('H:i'),
                'first_arrival_time' => $this->first_arrival_time,
                'interval_minutes' => $this->interval_minutes,
                'capacity_1' => $this->capacity_1,
                'capacity_2' => $this->capacity_2,
                'capacity_3_4' => $this->capacity_3_4,
                'capacity_5_6' => $this->capacity_5_6,
                'type'        => $this->session->type
            ];
//            $options->getMinutes = function(){
//                //13:00:00 > 13, 00
//                $timeInfo = explode(":", $this->time);
//                $hour = (int)$timeInfo[0];
//                $minute = (int)$timeInfo[1];
//
//                return $hour * 60 + $minute;
//            };

            $chunk->push($options);
            $count++;
            $start_time->addMinutes($interval);
        }
        //$this->chunk = 'fuck';

        return $chunk;
    }
    
    
}
