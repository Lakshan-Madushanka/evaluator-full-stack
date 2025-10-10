<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\TeamRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\deleteJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = deleteJson(route('api.v1.administrative.teams.delete', ['team' => 1]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/team/delete');

it('return 404 not found response for admin users', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = deleteJson(route('api.v1.administrative.teams.delete', ['team' => 1]));
    $response->assertNotFound();
})->group('api/v1/administrative/team/delete');

it('allows super admin users to delete team', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $team = TeamRepository::getRandomTeam();
    $teamsCount = TeamRepository::getTotalTeamsCount();

    $response = deleteJson(route('api.v1.administrative.teams.delete', ['team' => $team->hash_id]));
    $response->assertNoContent();

    assertDatabaseCount('teams', $teamsCount - 1);
})->group('api/v1/administrative/team/delete');
