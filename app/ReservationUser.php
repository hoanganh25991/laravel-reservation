<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;

/**
 * @property string password_hash
 *
 * @property mixed outlet_ids
 * @see App\ReservationUser::getOutletIdsAttribute
 *
 * @property mixed permission_level
 * @see App\ReservationUser::getPermissionLevelAttribute
 *
 * @property mixed id
 * 
 * @method notAdministrator
 * @see App\ReservationUser::scopeNotAdministrator
 * 
 * @method notCurrentUser
 * @see App\ReservationUser::scopeNotCurrentUser
 */
class ReservationUser extends User {
    
    use Notifiable;

    const RESERVATIONS  = 0;
    const ADMINISTRATOR = 10;

    protected $guarded = ['id'];

    /**
     * Protect model from unwanted column when build query
     */
    protected $fillable = [
        'user_name',
        'password_hash',
        'email',
        'display_name',
        'outlet_ids',
        'permission_level'
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];
    
    protected $appends = [
        'role'
    ];

    protected $table = 'outlet_reservation_user';

    protected $rememberTokenName = 'secret_token';

    public function getAuthPassword() {
        return $this->password_hash;
    }

    /**
     * Check if user can go to admin page
     * Users hard-coded in database
     * Who knows user_name/password can access admin page
     * @return bool
     */
    public function canAccessAdminPage(){
        return true;
    }

    /**
     * Get function for permission_level attribute
     * Consider null on permission level as 'Reservation'
     * @see App\ReservationUser::RESERVATIONS
     * @param $value
     * @return string
     */
    public function getPermissionLevelAttribute($value){
        if(is_null($value)){
            return ReservationUser::RESERVATIONS;
        }

        return $value;
    }

    public function setPermissionLevelAttribute($value){
        if(is_null($value)){
            $value = ReservationUser::RESERVATIONS;
        }

        $this->attributes['permission_level'] = $value;
    }

    /**
     * Reservations Role
     * Users in this role can modify on reservations
     */
    public function isReservations(){
        return $this->permission_level == ReservationUser::RESERVATIONS;
    }

    /**
     * Aadministrator Role
     * Users has this role can modify all configs
     */
    public function isAdministrator(){
        return $this->permission_level == ReservationUser::ADMINISTRATOR;
    }

    /**
     * Assigned outlets
     * @see App\ReservationUser::getOutletIdsAttribute
     * @return array
     */
    public function allowedOutletIds(){
        return $this->outlet_ids;
    }

    /**
     * Outlet ids when submit back from user may exist in 2 form
     * array|string >>> need transform into str
     * @param $value
     */
    public function setOutletIdsAttribute($value){
        $ids_str = $value;

        if(is_array($value)){
            $ids_str = implode(',', $value);
        }

        $this->attributes['outlet_ids'] = $ids_str;
    }

    /**
     * Parse assigned outlets str into array
     * @param $value
     * @return array
     */
    public function getOutletIdsAttribute($value){
        $ids_str = $value;

        if(is_null($ids_str)){
            return [];
        }

        $ids = preg_split('/\s*,\s*/', trim($ids_str));

        return $ids;
    }

    /**
     * Users not administrator
     * @warn NULL in mysql have to be query as a seperate condition
     *      CAN'T do math on null
     * @param $query
     * @return mixed
     */
    public function scopeNotAdministrator($query){
        return
            $query
                ->where('permission_level', '!=', ReservationUser::ADMINISTRATOR)
                ->orWhereNull('permission_level');
    }


    /**
     * All users, exclude the current
     * @param $query
     * @return mixed
     */
    public function scopeNotCurrentUser($query){
        /** @var ReservationUser $user */
        $user  = Auth::user();

        if(!is_null($user)){
            return $query->where('id', '!=', $user->id);
        }
        
        return $query;
    }

    /**
     * Get role name base on permission_level
     * @see App\ReservationUser::permission_level
     * @return string
     */
    public function getRoleAttribute(){
        if(is_null($this->permission_level)){
            return 'Logined';
        }
        
        switch($this->permission_level){
            case ReservationUser::RESERVATIONS:
                return 'Reservations';
            case ReservationUser::ADMINISTRATOR:
                return 'Administrator';
            default:
                return 'Logined';
        }
    }
}
