<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_a_basic_request(){
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_interacting_with_headers(){
        $response = $this->withHeaders([
            'X-Header' => 'Value',
        ])->withCookies([
            'color' => 'blue',
            'name' => 'Taylor',
        ])->get('/api/test');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // $response->dump();
    }

    public function test_that_users_bearer_token_is_correct(){
        $user = User::find(11);
        $token = $user->createToken('my-app-token')->plainTextToken;
        // $token = "yeyyeye";
         
        $response = $this->withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->get('/api/test_login');

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            
        ]);
        $response->assertJsonPath('details.user.email','ikechukwunwogo@gmail.com');
        $response->assertJson(fn (AssertableJson $json) => 
            $json->where('details.user.id',11)
                ->whereNot('details.user.email_verified_at',NULL)
                ->whereType('success','boolean')
            ->etc()
        );

        // $response->dump();
    }
}
