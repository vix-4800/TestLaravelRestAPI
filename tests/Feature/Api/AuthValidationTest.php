<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthValidationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_login_request_fails_for_non_existing_user(): void
    {
        $data = [
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ];

        $this->postJson(route('auth.login'), $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_request_validation(): void
    {
        $data = [
            'name' => $this->faker->randomLetter,
            'email' => $this->faker->sentence,
        ];

        $this->postJson(route('auth.register'), $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'email',
            ]);
    }
}
