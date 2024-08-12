<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Str;
use Tests\TestCase;

class PostValidationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

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

    public function test_store_request_validation(): void
    {
        $data = [
            'title' => $this->faker->randomLetter,
            'body' => Str::random(5),
        ];

        $this->postJson(route('posts.store'), $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'title',
                'body',
                'author_id',
            ]);
    }

    public function test_update_request_validation(): void
    {
        $data = [
            'title' => $this->faker->randomLetter,
            'body' => Str::random(5),
        ];

        $this->putJson(route('posts.update', ['post' => 1]), $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'title',
                'body',
            ]);
    }

    public function test_show_non_existing_post_request_fails(): void
    {
        $this->getJson(route('posts.show', ['post' => 999]))
            ->assertNotFound();
    }

    public function test_update_non_existing_post_request_fails(): void
    {
        $this->putJson(route('posts.update', ['post' => 999]))
            ->assertNotFound();
    }

    public function test_delete_non_existing_post_request_fails(): void
    {
        $this->deleteJson(route('posts.destroy', ['post' => 999]))
            ->assertNotFound();
    }
}
