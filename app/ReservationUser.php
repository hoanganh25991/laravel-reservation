<?php

namespace App;

use Illuminate\Notifications\Notifiable;

/**
 * @property string password_hash
 * @property mixed outlet_ids
 */
class ReservationUser extends User {
    
    use Notifiable;

    const RESERVATIONS  = 0;
    const ADMINISTRATOR = 10;

    protected $guarded = ['id'];

    protected $fillable = [
        'user_name',
        'password_hash',
        'email',
        'display_name'.
        'outlet_ids',
        'permission_level'
    ];

    protected $hidden = [
        'password_hash', 'remember_token',
    ];

    
    protected $table = 'outlet_reservation_user';

    
    protected $rememberTokenName = 'secret_token';


    public function getAuthPassword() {
        return $this->password_hash;
    }

    /**
     * @return array
     */
    public function allowedOutletIds(){
        $ids_str = $this->outlet_ids;

        if(is_null($ids_str)){
            return [];
        }

        $ids     = preg_split('/\s*,\s*/', trim($ids_str));
        
        return $ids;
    }
    
    
}
