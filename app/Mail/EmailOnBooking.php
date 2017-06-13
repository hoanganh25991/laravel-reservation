<?php

namespace App\Mail;

use App\Outlet;
use App\Reservation;
use App\Traits\CleanString;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailOnBooking extends Mailable {
    use Queueable, SerializesModels;
    use CleanString;

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
     * @return $this
     * @throws \Exception
     */
    public function build() {
        $outlet = $this->outlet;
        $reservation = $this->reservation;
        $address = env('MAIL_FROM_ADDRESS');
        // Config need explicit tell which email address used to send email
        // If nothing config, thrown exception
        if(is_null($address)){
            throw new \Exception('Please add MAIL_FROM_ADDRESS in .env file');
        }
        // In email, name sender with special character NOT ACCEPTED
        // Remove move it first
        $name    = $this->clean($outlet->outlet_name);
        $subject = $reservation->email_subject;

        return $this->from($address, $name)
                    ->subject($subject)
                    ->view('email.email-on-booking');
    }


}
