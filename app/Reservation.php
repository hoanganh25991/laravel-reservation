<?php

namespace App;

class Reservation extends HoiModel
{
    const RESERVED = 100;
    const REMINDER_SENT = 200;
    const CONFIRMED = 300;
    const ARRIVED = 400;
    const USER_CANCELLED = -100;
    const STAFF_CANCELLED = -200;
    const NO_SHOW = -300;

    protected $table = 'reservation';

}
