<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Mail\ResetPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_login_request_is_successful(): void
    {
        $password = $this->faker->password(8);
        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $data = [
            'email' => $user->email,
            'password' => $password,
        ];

        $this->postJson(route('auth.login'), $data)
            ->assertOk()
            ->assertJsonStructure([
                'auth_token',
                'token_type',
            ]);
    }

    public function test_register_request_is_successful(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password(8),
        ];

        $this->postJson(route('auth.register'), $data)
            ->assertCreated()
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'registered_at',
                ],
                'auth_token',
                'token_type',
            ]);
    }

    public function test_reset_password_request_is_successful(): void
    {
        Mail::fake();

        $password = $this->faker->password(8);
        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $data = [
            'email' => $user->email,
        ];

        $this->postJson(route('auth.reset-password'), $data)
            ->assertOk()
            ->assertJsonStructure([
                'message',
            ]);

        Mail::assertQueued(ResetPassword::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

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

    public function test_post_resource_is_not_accessible_for_unauthorized_users(): void
    {
        $this->postJson(route('posts.index'))
            ->assertUnauthorized();
    }

    public function test_users_resource_is_not_accessible_for_unauthorized_users(): void
    {
        $this->getJson(route('users.index'))
            ->assertUnauthorized();
    }
}
