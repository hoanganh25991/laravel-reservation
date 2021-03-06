<?php

namespace Tests\Feature;

use Auth;
use Artisan;
use Tests\TestCase;
use App\ReservationUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserLoginTest extends TestCase{
    use DatabaseTransactions;



    public function setUp(){
        parent::setUp(); // TODO: Change the autogenerated stub
        Artisan::call('migrate');
    }



    /** @test */
    public function it_should_allow_login_by_user_name(){
        $user   = factory(ReservationUser::class)->create(['password_hash' => sha1('a')]);

        $authed = Auth::attempt([
            'user_name' => $user->user_name,
            'password' => 'a'
        ]);

        $this->assertTrue($authed, 'Auth by user name failed');
    }
}
