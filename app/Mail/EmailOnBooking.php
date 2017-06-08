<?php

namespace App\Mail;

use App\Outlet;
use App\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailOnBooking extends Mailable {
    use Queueable, SerializesModels;

    /** @var Reservation  */
    public $reservation;
    /** @var Outlet  */
    public $outlet;

    /**
     * Create a new message instance.
     *
     * @param $reservation
     */
    public function __construct(Reservation $reservation) {
        $this->reservation = $reservation;
        $this->outlet = $reservation->outlet;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->view('email.email-on-booking');
    }
}
