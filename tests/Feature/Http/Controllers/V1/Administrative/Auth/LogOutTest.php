<?php

use App\Enums\Role;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\postJson;

it('return 401 response for non login users', function () {
    $response = postJson(route('api.v1.administrative.logout'));
    $response->assertUnauthorized();
})->group('administrative/users/logout');

it('allows login users to logout', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $response = sendLogoutRequest();
    $response->assertNoContent();

    expect(Auth::guard('web')->check())->toBeFalse();
})->group('administrative/users/logout');

function sendLogoutRequest(array $data = []): TestResponse
{
    return postJson(
        uri: route('api.v1.administrative.logout'),
        headers: [
            'withCredentials' => true,
            'accept' => 'application/json',
            'origin' => 'mcq.io',

        ]
    );
}
