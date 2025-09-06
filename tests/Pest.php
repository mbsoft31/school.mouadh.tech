<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Api\V1\ApiTestCase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/
uses(ApiTestCase::class)->in('Feature/Api');
//uses(TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Uses RefreshDatabase for all Feature tests
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeUuid', function () {
    return $this->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i');
});

expect()->extend('toHavePaginationStructure', function () {
    return $this->toHaveKeys(['current_page', 'last_page', 'per_page', 'total']);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

// Make sure you have this for authentication testing
function actingAsUser($user = null)
{
    $user = $user ?? User::factory()->create();
    Sanctum::actingAs($user, ['*']); // Add the abilities parameter
    return $user;
}
