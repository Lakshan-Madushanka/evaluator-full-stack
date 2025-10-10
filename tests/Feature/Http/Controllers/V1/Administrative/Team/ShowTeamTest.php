<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\TeamRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\getJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = getJson(route('api.v1.administrative.teams.show', ['team' => 1]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/team/show');

it('throw 404 administrative users to when try to access team using direct id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $teamId = TeamRepository::getRandomTeam()->id;

    $response = getJson(route('api.v1.administrative.teams.show', ['team' => $teamId]));
    $response->assertNotFound();
})->group('api/v1/administrative/team/show');

it('allows administrative users to a team by hash id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $teamHashId = TeamRepository::getRandomTeam()->hash_id;

    $response = getJson(route('api.v1.administrative.teams.show', ['team' => $teamHashId]));
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type', 'data.attributes')
        ->where('data.id', $teamHashId)
        ->etc()
    );
})->group('api/v1/administrative/team/show');
