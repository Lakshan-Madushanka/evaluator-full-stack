<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\TeamRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\postJson;

it('return 401 response non-login users ', function () {
    $response = postJson(route('api.v1.administrative.teams.users.detach',
        ['team' => 'abc']));
    $response->assertUnauthorized();
})->group('administrative/team/user/detach');

it('return 404 response regular login users', function () {
    $user = UserRepository::getRandomUser();
    Sanctum::actingAs($user);

    $response = postJson(route('api.v1.administrative.teams.users.detach', ['team' => 'abc']));
    $response->assertNotFound();
})->group('administrative/team/user/detach');

test('it throws 422 error when trying to detach non existing user', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $team = TeamRepository::getRandomTeam();

    $response = postJson(
        route('api.v1.administrative.teams.users.detach', ['team' => $team?->hash_id]),
        ['userIds' => [-12]]
    );

    $response->assertInvalid('userIds.0');
})->group('administrative/team/user/detach');

test('it detach existing user', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $team = TeamRepository::getRandomTeam();

    $teamUsers = $team->users;
    $detachUserIds = [$teamUsers[0]->hash_id, $teamUsers[1]->hash_id];

    $response = postJson(
        route('api.v1.administrative.teams.users.detach', ['team' => $team?->hash_id]),
        ['userIds' => $detachUserIds]
    );

    $newTeamUsers = $team->users->fresh();

    $response->assertNoContent();

    expect(array_intersect($newTeamUsers->pluck('ids')->toArray(), $detachUserIds))->toBe([]);
})->group('administrative/team/user/detach');
