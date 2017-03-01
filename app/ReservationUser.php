<?php

namespace App;

use App\Events\RememberTokenEvent;
use Illuminate\Notifications\Notifiable;

class ReservationUser extends User
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_name', 'password_hash', 'email', 'display_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $table = 'outlet_reservation_user';

    protected $rememberTokenName = 'secret_token';

    public function __construct(array $attributes = []){
        parent::__construct($attributes);
        //$this->rememberTokenName = 'secret_token';
    }
}
