<?php

namespace App\Jobs;

use App\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendConfirmSMS implements ShouldQueue{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reservation;

    /**
     * Create a new job instance.
     *
     * @param Reservation $reservation
     */
    public function __construct(Reservation $reservation){

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){

    }
}
