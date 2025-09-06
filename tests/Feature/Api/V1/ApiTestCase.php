<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable throttling
        $this->withoutMiddleware([
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ]);

        // Use migrate:fresh to ensure clean database
        $this->artisan('migrate:fresh'); // This clears all data

        // Don't run seeders unless explicitly needed
        // $this->seed(); // Remove this if you have it

        $this->user = User::factory()->create();
    }

    protected function actingAsUser($user = null): User
    {
        $user = $user ?? User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        return $user;
    }

    // ... rest of your methods
}
