<?php

namespace Tests\Browser;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testLoginExample()
    {
        // $this->browse(function (Browser $browser) {
        //     $browser->visit('/')
        //             ->assertSee('Go-Easybiz');
        // });

        

        $this->browse(function ($browser)  {
            $browser->visit('/login')
                // ->type('country', '151')
                ->type('phone', '07051942325')
                ->type('password', 'Dave1614..') 
                // ->type('remember_me', true)
                ->press('login-btn')
                ->assertPathIs(RouteServiceProvider::HOME);
        });
    }
}
