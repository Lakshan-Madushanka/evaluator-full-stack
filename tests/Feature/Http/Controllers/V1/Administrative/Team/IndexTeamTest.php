<?php

use App\Enums\Role;
use App\Models\Team;
use Illuminate\Support\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\TeamRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->route = route('api.v1.administrative.teams.index');
});

it('return 401 unauthorized response for non-login users', function () {
    $response = getJson($this->route);
    $response->assertUnauthorized();
})->group('api/v1/administrative/team/index');

it('allows administrative users to retrieve all teams', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $teamsCount = TeamRepository::getTotalTeamsCount();

    $response = getJson($this->route);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', $teamsCount)->etc());
})->group('api/v1/administrative/team/index');

it('sorts all teams by name asc order by default', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $teamsCount = TeamRepository::getTotalTeamsCount();

    $response = getJson($this->route);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', $teamsCount)->etc());

    $data = $response->decodeResponseJson()['data'];
    $data = collect($data)->pluck('attributes.name');
    $sortedData = $data->sort();

    expect($data->all())->toEqual($sortedData->all());
})->group('api/v1/administrative/team/index');

it('can sorts all teams by name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $teamsCount = TeamRepository::getTotalTeamsCount();

    $query = '?'.http_build_query([
        'sort' => '-name',
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', $teamsCount)->etc());

    $data = $response->decodeResponseJson()['data'];
    $data = collect($data)->pluck('attributes.name');
    $sortedData = $data->sortByDesc('name');

    expect($data->all())->toEqual($sortedData->all());
})->group('api/v1/administrative/team/index');

it('can filter all teams by name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $name = Str::random();
    Team::create(['name' => $name]);

    $query = '?'.http_build_query([
        'filter' => ['name' => $name],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->pluck('attributes.name')->each(function (string $catName) use ($name) {
        expect(str_contains($catName, $name))->toBeTrue();
    });
})->group('api/v1/administrative/team/index');

it('can sorts all teams by created at column', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $teamsCount = TeamRepository::getTotalTeamsCount();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $query = '?'.http_build_query([
        'sort' => '-created_at',
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', $teamsCount)->etc());

    $data = $response->decodeResponseJson()['data'];
    $data = collect($data)->pluck('attributes.created_at')->map(function ($created_at) {
        return Carbon::parse($created_at)->getTimestamp();
    });

    $sortedData = $data->sortDesc()->values();

    expect($data->all())->toBe($sortedData->all());
})->group('api/v1/administrative/team/index');
