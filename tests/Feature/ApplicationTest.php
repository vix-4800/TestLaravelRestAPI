<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function test_application_is_up(): void
    {
        $this->get('/up')
            ->assertOk()
            ->assertSee('Application up');
    }
}
