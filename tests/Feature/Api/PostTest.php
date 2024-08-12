<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Events\PostCreated as PostCreatedEvent;
use App\Listeners\SendPostCreatedNotification;
use App\Mail\PostCreated;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
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

    public function test_index_request_is_successful(): void
    {
        $this->getJson(route('posts.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->postCollectionStructure,
                ],
            ]);
    }

    public function test_store_request_is_successful(): void
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

    public function test_after_store_request_email_is_sent(): void
    {
        Mail::fake();

        $data = [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
            'author' => $this->user->id,
        ];

        $this->postJson(route('posts.store'), $data);

        Mail::assertQueued(PostCreated::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    public function test_post_created_event_is_being_listened(): void
    {
        Event::fake();

        Event::assertListening(
            PostCreatedEvent::class,
            SendPostCreatedNotification::class
        );
    }

    public function test_show_request_is_successful(): void
    {
        $this->getJson(route('posts.show', ['post' => 1]))
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->postCollectionStructure,
            ]);
    }

    public function test_update_request_is_successful(): void
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

    public function test_delete_request_is_successful(): void
    {
        $this->deleteJson(route('posts.destroy', ['post' => 1]))
            ->assertNoContent();
    }
}
