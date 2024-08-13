<?php

declare(strict_types=1);

namespace App\Events;

use App\Enums\RequestMethod;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserActivityLogged
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly string $url,
        public readonly RequestMethod $method,
        public readonly int $responseCode,
        public readonly string $ipAddress,
    ) {
        //
    }
}
