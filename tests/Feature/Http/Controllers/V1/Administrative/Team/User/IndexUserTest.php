<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\TeamRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\getJson;

it('return 401 response non-login users ', function () {
    $team = TeamRepository::getRandomTeam();

    $response = getJson(route('api.v1.administrative.teams.users.index', ['team' => $team->hash_id]));
    $response->assertUnauthorized();
})->group('administrative/team/user/index');

it('return 404 response regular login users', function () {
    $user = UserRepository::getRandomUser();
    Sanctum::actingAs($user);

    $team = TeamRepository::getRandomTeam();

    $response = getJson(route('api.v1.administrative.teams.users.index', ['team' => $team->hash_id]));
    $response->assertNotFound();
})->group('administrative/team/user/index');

test('admin can obtain all team users', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $team = TeamRepository::getRandomTeam();

    $response = getJson(route('api.v1.administrative.teams.users.index', ['team' => $team->hash_id]));
    $response->assertOk();
})->group('administrative/team/user/index');

test('can paginate user records', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $team = TeamRepository::getRandomTeam();

    $query = '?'.http_build_query(['page' => ['size' => 1]]);

    $route = route('api.v1.administrative.teams.users.index', ['team' => $team?->hash_id]).$query;
    $response = getJson($route);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', 1)
        ->hasAll(['links', 'meta', 'meta.current_page'])
        ->etc());

    $response->assertJsonPath('meta.per_page', 1);
})->group('administrative/team/user/index');
