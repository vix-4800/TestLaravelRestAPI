<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserActivityLogged;

class LogUserActivity
{
    /**
     * Handle the event.
     */
    public function handle(UserActivityLogged $event): void
    {
        $event->user->activities()->create([
            'url' => $event->url,
            'method' => $event->method,
            'response_code' => $event->responseCode,
            'ip_address' => $event->ipAddress,
        ]);
    }
}
