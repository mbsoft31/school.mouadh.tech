<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        $this->user = User::factory()->create();
    }

    function actingAsUser($user = null)
    {
        $user = $user ?? User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        test()->withHeaders(['Authorization' => 'Bearer ' . $token]);
        return $user;
    }

    protected function assertApiError(object $response, int $status, ?string $errorCode = null): void
    {
        $response->assertStatus($status);

        if ($errorCode) {
            $response->assertJson(['error_code' => $errorCode]);
        }
    }

    protected function assertApiSuccess(object $response, int $status = 200): void
    {
        $response->assertStatus($status);
    }
}


// tests/Pest.php
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

uses(TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Uses RefreshDatabase for all Feature tests
|--------------------------------------------------------------------------
*/

uses(RefreshDatabase::class)->in('Feature');

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
----
// tests/TestCase.php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    //
}
---
// Terminal Output


"C:\Program Files\php-8.4.7-Win32-vs17-x64\php.exe" D:\research\applications\school.mouadh.tech\vendor\pestphp\pest\bin\pest --teamcity --configuration D:\research\applications\school.mouadh.tech\phpunit.xml

Failed asserting that 40 is identical to 25.
at tests\Feature\Api\V1\CourseApiTest.php:126

Failed asserting that 8 is identical to 1.
at tests\Feature\Api\V1\CourseApiTest.php:69

Failed asserting that 3 is identical to 1.
at tests\Feature\Api\V1\CourseApiTest.php:81

Failed asserting that 2 is identical to 1.
at tests\Feature\Api\V1\CourseApiTest.php:57

Failed asserting that 16 is identical to 1.
at tests\Feature\Api\V1\CourseApiTest.php:148

Expected response status code [201, 301, 302, 303, 307, 308] but received 429.
Failed asserting that false is true.
at vendor\laravel\framework\src\Illuminate\Testing\TestResponseAssert.php:45
at vendor\laravel\framework\src\Illuminate\Testing\TestResponse.php:199
at tests\Feature\Auth\EmailVerificationTest.php:88

The expected [Illuminate\Auth\Events\Verified] event was not dispatched.
Failed asserting that false is true.
at vendor\laravel\framework\src\Illuminate\Support\Testing\Fakes\EventFake.php:144
at vendor\laravel\framework\src\Illuminate\Support\Facades\Facade.php:363
at tests\Feature\Auth\EmailVerificationTest.php:29

BadMethodCallException: Call to undefined method Database\Factories\AssessmentAttemptFactory::completed()
at vendor\laravel\framework\src\Illuminate\Support\Traits\ForwardsCalls.php:67
at vendor\laravel\framework\src\Illuminate\Database\Eloquent\Factories\Factory.php:1036
at tests\Feature\Api\V1\AssessmentAttemptApiTest.php:90

BadMethodCallException: Call to undefined method Database\Factories\AssessmentAttemptFactory::completed()
at vendor\laravel\framework\src\Illuminate\Support\Traits\ForwardsCalls.php:67
at vendor\laravel\framework\src\Illuminate\Database\Eloquent\Factories\Factory.php:1036
at tests\Feature\Api\V1\AssessmentAttemptApiTest.php:165

Failed asserting that 1 is identical to 1.0.
at tests\Feature\Api\V1\AssessmentAttemptApiTest.php:216

Failed asserting that '004227d1-0dee-4ab8-925c-e7ede4b2f84e' is identical to an object of class "Ramsey\Uuid\Lazy\LazyUuidFromString".
at tests\Feature\Api\V1\AssessmentAttemptApiTest.php:57

Session is missing expected key [errors].
Failed asserting that false is true.
at vendor\laravel\framework\src\Illuminate\Testing\TestResponseAssert.php:45
at vendor\laravel\framework\src\Illuminate\Testing\TestResponse.php:1517
at vendor\laravel\framework\src\Illuminate\Testing\TestResponse.php:1594
at tests\Feature\Settings\PasswordUpdateTest.php:48

Expected response status code [201, 301, 302, 303, 307, 308] but received 429.
Failed asserting that false is true.
at vendor\laravel\framework\src\Illuminate\Testing\TestResponseAssert.php:45
at vendor\laravel\framework\src\Illuminate\Testing\TestResponse.php:199
at tests\Feature\Settings\PasswordUpdateTest.php:30

BadMethodCallException: Call to undefined method Database\Factories\LessonProgressFactory::completed()
at vendor\laravel\framework\src\Illuminate\Support\Traits\ForwardsCalls.php:67
at vendor\laravel\framework\src\Illuminate\Database\Eloquent\Factories\Factory.php:1036
at tests\Feature\Api\V1\ProgressApiTest.php:28

Failed asserting that '7345069c-5825-4140-b386-207274a25180' is identical to an object of class "Ramsey\Uuid\Lazy\LazyUuidFromString".
at tests\Feature\Api\V1\ProgressApiTest.php:129

BadMethodCallException: Call to undefined method Database\Factories\LessonProgressFactory::completed()
at vendor\laravel\framework\src\Illuminate\Support\Traits\ForwardsCalls.php:67
at vendor\laravel\framework\src\Illuminate\Database\Eloquent\Factories\Factory.php:1036
at tests\Feature\Api\V1\ProgressApiTest.php:87

Tests:    16 failed, 48 passed (388 assertions)
Duration: 15.95s
Random Order Seed: 1757180363


Process finished with exit code 2
