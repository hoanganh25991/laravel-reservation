<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends HoiModel implements AuthenticatableContract, 
                                           AuthorizableContract, 
                                           CanResetPasswordContract
{

    use Authenticatable, Authorizable, CanResetPassword;

    protected $table = 'outlet_reservation_user';
    
    
    public function __construct(array $attributes = []){
        parent::__construct($attributes);
        $this->rememberTokenName = 'secret_token';
    }



    

}