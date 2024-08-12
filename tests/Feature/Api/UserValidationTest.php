<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserValidationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorize();
    }

    /**
     * Authorize the user.
     */
    protected function authorize(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
    }

    public function test_store_request_validation(): void
    {
        $data = [
            'name' => $this->faker->randomLetter,
            'email' => $this->faker->sentence,
        ];

        $this->postJson(route('users.store'), $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password',
            ]);
    }

    public function test_update_request_validation(): void
    {
        $data = [
            'name' => $this->faker->randomLetter,
            'email' => $this->faker->sentence,
        ];

        $this->putJson(route('users.update', ['user' => 1]), $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'email',
            ]);
    }

    public function test_show_non_existing_users_request(): void
    {
        $this->getJson(route('users.show', ['user' => 999]))
            ->assertNotFound();
    }

    public function test_update_non_existing_users_request(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        $this->putJson(route('users.update', ['user' => 999]), $data)
            ->assertNotFound();
    }

    public function test_delete_non_existing_users_request(): void
    {
        $this->deleteJson(route('users.destroy', ['user' => 999]))
            ->assertNotFound();
    }
}
