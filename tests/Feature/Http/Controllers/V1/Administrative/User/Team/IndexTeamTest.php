<?php

use App\Enums\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\getJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = getJson(route('api.v1.administrative.users.teams.index', ['user' => 'abc']));
    $response->assertUnauthorized();
})->group('api/v1/administrative/user/team/index');

it('allows administrative users to retrieve all teams of a user', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $user = User::factory()
        ->has(Team::factory()->count(3))
        ->create();

    $user->teams()->create(Team::factory()->make(['name' => 'aaaaaa'])->toArray());

    $response = getJson(route('api.v1.administrative.users.teams.index', ['user' => $user->hash_id]));
    $response->assertOk();

    $response
        ->assertJson(fn (AssertableJson $json) => $json->has('data', 4)
            ->has('data.0', fn (AssertableJson $json) => $json->where('attributes.name', 'aaaaaa')
                ->etc()
            )
            ->etc()
        );
})->group('api/v1/administrative/user/team/index');

it('sorts all teams by name asc order by default', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $user = User::factory()
        ->has(Team::factory()->count(3))
        ->create();

    $response = getJson(route('api.v1.administrative.users.teams.index', ['user' => $user->hash_id]));
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', 3)->etc());

    $data = $response->decodeResponseJson()['data'];
    $data = collect($data)->pluck('attributes.name');
    $sortedData = $data->sort();

    expect($data->all())->toEqual($sortedData->all());
})->group('api/v1/administrative/user/team/index');

it('can sorts all teams by name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $user = User::factory()
        ->has(Team::factory()->count(3))
        ->create();

    $query = '?'.http_build_query([
        'sort' => '-name',
    ]);

    $response = getJson(route('api.v1.administrative.users.teams.index', ['user' => $user->hash_id]).$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', 3)->etc());

    $data = $response->decodeResponseJson()['data'];
    $data = collect($data)->pluck('attributes.name');
    $sortedData = $data->sortByDesc('name');

    expect($data->all())->toEqual($sortedData->all());
})->group('api/v1/administrative/user/team/index');

it('can filter all teams by name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $user = User::factory()
        ->has(Team::factory()->count(3))
        ->create();

    $user->teams()->create(Team::factory()->make(['name' => 'bbbb'])->toArray());

    $query = '?'.http_build_query([
        'filter' => ['name' => 'bbbb'],
    ]);

    $response = getJson(route('api.v1.administrative.users.teams.index', ['user' => $user->hash_id]).$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->pluck('attributes.name')->each(function (string $catName) {
        expect(str_contains($catName, 'bbbb'))->toBeTrue();
    });
})->group('api/v1/administrative/user/team/index');

it('can sorts all teams by created at column', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $user = User::factory()
        ->has(Team::factory()->count(3))
        ->create();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $query = '?'.http_build_query([
        'sort' => '-created_at',
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson(route('api.v1.administrative.users.teams.index', ['user' => $user->hash_id]).$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', 3)->etc());

    $data = $response->decodeResponseJson()['data'];
    $data = collect($data)->pluck('attributes.created_at')->map(function ($created_at) {
        return Carbon::parse($created_at)->getTimestamp();
    });

    $sortedData = $data->sortDesc()->values();

    expect($data->all())->toBe($sortedData->all());
})->group('api/v1/administrative/user/team/index');
