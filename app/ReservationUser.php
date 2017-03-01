<?php

namespace App;

use Illuminate\Notifications\Notifiable;

/**
 * @property string password_hash
 */
class ReservationUser extends User {
    
    use Notifiable;

    
    
    protected $fillable = [
        'user_name', 'password_hash', 'email', 'display_name'
    ];

    protected $hidden = [
        'password_hash', 'remember_token',
    ];

    
    protected $table = 'outlet_reservation_user';

    
    protected $rememberTokenName = 'secret_token';


    public function getAuthPassword() {
        return $this->password_hash;
    }
    
    
    
    
}
