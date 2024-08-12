<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class UserService
{
    /**
     * Clear the cache for users.
     */
    public function clearCache(string $key = 'users'): void
    {
        Cache::forget($key);
    }
}
