<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PostCreated;
use App\Mail\PostCreated as MailPostCreated;
use Illuminate\Support\Facades\Mail;

class SendPostCreatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(PostCreated $event): void
    {
        $user = $event->post->author;

        Mail::to($user->email)->send(new MailPostCreated($event->post));
    }
}
