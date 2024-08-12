<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private array $userCollectionStructure = [
        'id',
        'name',
        'email',
        'registered_at',
    ];

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

    public function test_index_request_is_successful(): void
    {
        $this->getJson(route('users.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [$this->userCollectionStructure],
            ]);
    }

    public function test_store_request_is_successful(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password(8),
        ];

        $this->postJson(route('users.store', $data))
            ->assertCreated()
            ->assertJsonStructure([
                'data' => $this->userCollectionStructure,
            ]);
    }

    public function test_show_request_is_successful(): void
    {
        $this->getJson(route('users.show', ['user' => 1]))
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->userCollectionStructure,
            ]);
    }

    public function test_update_request_is_successful(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        $this->putJson(route('users.update', ['user' => 1], $data))
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->userCollectionStructure,
            ]);
    }

    public function test_delete_request_is_successful(): void
    {
        $this->deleteJson(route('users.destroy', ['user' => 1]))
            ->assertNoContent();
    }
}
