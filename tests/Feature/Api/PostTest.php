<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    private array $postCollectionStructure = [
        'id',
        'title',
        'body',
        'author',
        'created_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorize();

        $this->user->posts()->create([
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
        ]);
    }

    /**
     * Authorize the user.
     */
    protected function authorize(): void
    {
        $this->user = User::factory()->create();

        $this->actingAs($this->user);
    }

    public function test_api_posts_index_request_is_successful(): void
    {
        $this->getJson(route('posts.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->postCollectionStructure,
                ],
            ]);
    }

    public function test_api_posts_store_request_is_successful(): void
    {
        $data = [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
            'author' => $this->user->id,
        ];

        $this->postJson(route('posts.store'), $data)
            ->assertCreated()
            ->assertJsonStructure([
                'data' => $this->postCollectionStructure,
            ]);
    }

    public function test_api_posts_show_request_is_successful(): void
    {
        $this->getJson(route('posts.show', ['post' => 1]))
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->postCollectionStructure,
            ]);
    }

    public function test_api_posts_update_request_is_successful(): void
    {
        $data = [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
        ];

        $this->putJson(route('posts.update', ['post' => 1]), $data)
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->postCollectionStructure,
            ]);
    }

    public function test_api_posts_delete_request_is_successful(): void
    {
        $this->deleteJson(route('posts.destroy', ['post' => 1]))
            ->assertNoContent();
    }
}
