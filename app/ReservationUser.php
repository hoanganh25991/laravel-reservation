<?php

namespace App;

use Illuminate\Notifications\Notifiable;

/**
 * @property string password_hash
 * @property mixed outlet_ids
 * @property mixed permission_level
 * 
 * @method notAdministrator
 * @see ReservationUser::scopeNotAdministrator
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
     * @return bool
     */
    public function canAccessAdminPage(){
        return !is_null($this->permission_level);
    }

    /**
     * Any one can go to register page
     * To regis as an user
     * BUT only staff with permission can go
     * 
     * At this point
     * Consider staff as RESERVATIONS level
     */
    public function isReservations(){
//        return $this->permission_level == ReservationUser::RESERVATIONS;
        return $this->canAccessAdminPage();
    }

    /**
     * Only administrator can change config
     */
    public function isAdministrator(){
        return $this->permission_level == ReservationUser::ADMINISTRATOR;
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
    
    public function scopeNotAdministrator($query){
        return
            $query
                ->where('permission_level', '!=', ReservationUser::ADMINISTRATOR)
                ->orWhereNull('permission_level');
    }
}
