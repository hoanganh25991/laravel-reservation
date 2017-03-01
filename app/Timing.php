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
 * @property mixed capacity_7_x
 * @property mixed max_pax
 */
class Timing extends Model {

    /**
     * Interval minute for user pick time
     * must follow these value
     */
    const INTERVAL_MINUTE_STEPS = [15, 20, 30, 60];

    /**
     * First arrival time & last arrival time pick rule
     */
    const ARRIVAL_STEPS  = [15];

    protected $table = 'timing';

    /**
     * Relationship with session
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function session(){
        return $this->hasOne(Session::class, 'id', 'session_id');
    }

    /**
     * Timing decide how to chunk base on its arrival time
     * @return \Illuminate\Support\Collection
     */
    public function getChunkAttribute(){
        $allow_steps = array_merge(self::INTERVAL_MINUTE_STEPS, self::ARRIVAL_STEPS);

        $minimum_interval_to_match = GCD::find($allow_steps);

        $start_time = Carbon::createFromFormat('H:i:s', $this->first_arrival_time);
        $end_time   = Carbon::createFromFormat('H:i:s', $this->last_arrival_time);

        $chunks = collect([]);

        while($start_time->lt($end_time)){

            $chunk = (object)[
                'time'               => $start_time->format('H:i'),
                'session_type'       => $this->session->type,
                'session_name'       => $this->session->session_name,
                'first_arrival_time' => $this->first_arrival_time,
                'interval_minutes'   => $this->interval_minutes,
                'capacity_1'         => $this->capacity_1,
                'capacity_2'         => $this->capacity_2,
                'capacity_3_4'       => $this->capacity_3_4,
                'capacity_5_6'       => $this->capacity_5_6,
                'capacity_7_x'       => $this->capacity_7_x,
                'max_pax'            => $this->max_pax,
            ];

            $chunks->push($chunk);

            $start_time->addMinutes($minimum_interval_to_match);
        }

        return $chunks;
    }



    

}
