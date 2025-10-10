<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->route = route('api.v1.administrative.users.index');
});

it('return 401 response non-login users ', function () {
    $user = UserRepository::getRandomUser();

    $response = getJson(route('api.v1.administrative.users.index'));
    $response->assertUnauthorized();
})->group('administrative/users/index');

it('return 404 response regular login users', function () {
    $user = UserRepository::getRandomUser();
    Sanctum::actingAs($user);

    $response = getJson(route('api.v1.administrative.users.index'));
    $response->assertNotFound();
})->group('administrative/users/index');

test('super admin can obtain user records', function () {
    $user = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($user);

    $response = getJson(route('api.v1.administrative.users.index'));
    $response->assertOk();
})->group('administrative/users/index');

test('admin can obtain user records', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $response = getJson(route('api.v1.administrative.users.index'));
    $response->assertOk();
})->group('administrative/users/index');

test('can paginate user records', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $query = http_build_query(['page' => ['size' => 1]]);

    $response = \Pest\Laravel\json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', 1)
        ->hasAll(['links', 'meta', 'meta.current_page'])
        ->missing('data.0.attributes.password')
        ->etc());

    $response->assertJsonPath('meta.per_page', 1);
})->group('administrative/users/index');

test('can filter user records by role name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $query = http_build_query([
        'filter' => ['role' => Role::SUPER_ADMIN->name],
    ]);

    $response = \Pest\Laravel\json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    collect($results)->pluck('attributes.role')->each(function (string $role) {
        expect($role)->toBe(Role::SUPER_ADMIN->name);
    });
})->group('administrative/users/index');

test('can sort user records by created at column', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $query = http_build_query([
        'sort' => 'created_at',
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = \Pest\Laravel\json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    $resultsCreatedAtTimestamps = collect($results)->transform(function (array $data) {
        return \Carbon\Carbon::parse($data['attributes']['created_at'])->getTimestamp();
    });

    $sortedTimeStamps = $resultsCreatedAtTimestamps->sort()->values()->all();

    expect($resultsCreatedAtTimestamps->toArray() === $sortedTimeStamps)->toBeTrue();
})->group('administrative/users/index');

test('can filter user by exact name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $userName = UserRepository::getRandomUser()->name;

    $query = http_build_query([
        'filter' => ['name' => $userName],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = \Pest\Laravel\json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];
    collect($results)->each(fn (array $user) => expect($user['attributes']['name'])->toBe($userName));
})->group('administrative/users/index');

test('can filter user by partial name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $userName = 'test user';

    \App\Models\User::factory()->create(['name' => $userName]);

    $query = http_build_query([
        'filter' => ['name' => 'use'],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = \Pest\Laravel\json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    collect($results)->each(fn (array $user) => expect(\Illuminate\Support\Str::contains($user['attributes']['name'], 'use'))->toBeTrue());
})->group('administrative/users/index');

test('can filter user by exact email', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $email = UserRepository::getRandomUser()->email;

    $query = http_build_query([
        'filter' => ['email' => $email],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = \Pest\Laravel\json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];
    collect($results)->each(fn (array $user) => expect($user['attributes']['email'])->toBe($email));
})->group('administrative/users/index');

test('can filter user by partial email', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $email = Str::random().'@mail.com';

    \App\Models\User::factory()->create(['email' => $email]);

    $partialEmail = \Illuminate\Support\Str::substr($email, 2, 8);

    $query = http_build_query([
        'filter' => ['email' => $partialEmail],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = \Pest\Laravel\json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    collect($results)->each(fn (array $user) => expect(\Illuminate\Support\Str::contains($user['attributes']['email'], $partialEmail))->toBeTrue());
})->group('administrative/users/index');
