<?php

namespace App;

use Illuminate\Http\Request;
use App\Http\Requests\ApiRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use App\OutletReservationSetting as Setting;

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
 * @property mixed brand_id
 * @property mixed display_name
 * @property mixed role
 * @see App\ReservationUser::getRoleAttribute
 *
 * @method notAdministrator
 * @see App\ReservationUser::scopeNotAdministrator
 * 
 * @method notCurrentUser
 * @see App\ReservationUser::scopeNotCurrentUser
 */
class ReservationUser extends User {
    
    use Notifiable;

    const RESERVATIONS        = 0;
    const MASTER_RESERVATIONS = 5;
    const ADMINISTRATOR       = 10;

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

    protected $table = 'res_outlet_reservation_user';

    protected $rememberTokenName = 'secret_token';

    //public static $should_scope_by_brand_id = true;

    /**
     * Inject into boot process
     * To modify on query scope or
     * Listen eloquent event : creating, saving, updating,...
     */
    protected static function boot() {
        parent::boot();

//        if(static::$should_scope_by_brand_id){
//            static::byBrandId();
//        }
    }

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
        $is_reservations = $this->permission_level == ReservationUser::RESERVATIONS;

        // Administrator over the reservations role
        // If it is administrator >>> can use any role from reservations
        $is_admin = $this->isAdministrator();
        // Master reservations has hight permission
        // Override on reservations only
        $is_master_reservations = $this->isMasterReservations();

        return $is_admin || $is_master_reservations|| $is_reservations;
    }

    /**
     * Master reservations role
     */
    public function isMasterReservations(){
        $is_master_reservations = $this->permission_level == ReservationUser::MASTER_RESERVATIONS;

        // Administrator over the reservations role
        // If it is administrator >>> can use any role from reservations
        $is_admin = $this->isAdministrator();

        return $is_admin || $is_master_reservations;
    }

    /**
     * Aadministrator Role
     * Users has this role can modify all configs
     */
    public function isAdministrator(){
        $is_admin = $this->permission_level == ReservationUser::ADMINISTRATOR;
        return $is_admin;
    }

    /**
     * Assigned outlets
     * @see App\ReservationUser::getOutletIdsAttribute
     * @return Collection
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
     * @return Collection
     */
    public function getOutletIdsAttribute($value){
        $ids_str = $value;

        if(is_null($ids_str)){
            return [];
        }

        $ids = preg_split('/\s*,\s*/', trim($ids_str));
        
        //love collection to wrap ids
        $ids_c = collect($ids); 

        return $ids_c;
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

        switch($this->permission_level){
            case ReservationUser::RESERVATIONS:
                return 'Reservations';
            case ReservationUser::ADMINISTRATOR:
                return 'Administrator';
            default:
                return 'Logined';
        }

    }

    /**
     * When user logined in, he only assign to specify brand_id
     * Base on this info, inject this infomation to global query scope
     */
    public function injectBrandId(){
        $brand_id = $this->brand_id;

        if(is_null($brand_id)){
            throw new \Exception('User not assigned brand_id, can not determine allowed him move on or not');
        }
        //Have to inject
        Setting::injectBrandId($brand_id);
    }

    /**
     * Convenience call to fetch all outlets he can access
     */
    public function outletsCanAccess(){
        $outlet_ids = $this->allowedOutletIds();
        $outlets    = Outlet::withoutGlobalScope('brand_id')->whereIn('id', $outlet_ids)->get();
        
        return $outlets;
    }

    /**
     * Admin role used for piece of function call, not a WHOLE controller
     * Like void|charge only for admin
     * >>> Check on each function call better than check at controller level
     * @return bool
     */
    public function hasAdministratorPermissionOnCurrentOutlet(){
        try {
            $outlet_id      = Setting::outletId();
            $isAdmin        = $this->isAdministrator();
            $allowed_outlet = $this->allowedOutletIds()->contains($outlet_id);

            return $isAdmin && $allowed_outlet;

        } catch(\Exception $e){

            return false;

        }
    }
    
    public function hasReservationsPermissionOnCurrentOutlet(){
        try {
            $outlet_id      = Setting::outletId();
            $isResv         = $this->isReservations();
            $allowed_outlet = $this->allowedOutletIds()->contains($outlet_id);

            return $isResv && $allowed_outlet;

        } catch(\Exception $e){

            return false;

        }
    }

    public function hasMasterReservationsPermissionOnCurrentOutlet(){
        try {
            $outlet_id      = Setting::outletId();
            $isMasterResv   = $this->isMasterReservations();
            $allowed_outlet = $this->allowedOutletIds()->contains($outlet_id);

            return $isMasterResv && $allowed_outlet;

        } catch(\Exception $e){

            return false;

        }
    }


    
}
