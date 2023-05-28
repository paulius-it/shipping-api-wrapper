<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserAccessControllerTest extends TestCase
{
    use refreshDatabase;

    /**
     * User registration test to see if he gets a token if registered/logged-in successfully
     */
    public function test_user_register_successful(): void
    {
        $userData = [
            'name' => '[[ Some name of the user ]]',
            'email' => 'name@email.com',
            'password' => 'StrongPassword!',
            'password_confirmation' => 'StrongPassword!',
        ];

        $response = $this->post(route('user.register', $userData))
            ->assertStatus(201); // created record
    }

    public function test_registered_user_gets_access_token(): void
    {
        $userData = [
            'name' => '[[ Some name of the user ]]',
            'email' => 'name@email.com',
            'password' => 'StrongPassword!',
            'password_confirmation' => 'StrongPassword!',
        ];

        $response = $this->post(route('user.register', $userData))
            ->assertStatus(201)
            ->assertJsonStructure(['access_token']);
    }

    public function test_user_cannot_register_with_wrong_credentials_provided(): void
    {
        $userData = [
            'name' => '[[ Some name of the user ]]',
            'email' => 'name@email.com',
            'password' => 'StrongPassword!',
            'password_confirmation' => 'WrongPassword!',
        ];

        $response = $this->postJson(route('user.register', $userData))
            ->assertStatus(422)
            ->assertJsonStructure(['errors']);
    }
}
