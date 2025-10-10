<?php

use App\Enums\Role;
use App\Models\Team;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\TeamRepository;
use Tests\Repositories\UserRepository;
use Vinkla\Hashids\Facades\Hashids;

use function Pest\Laravel\postJson;

it('return 401 response non-login users ', function () {
    $response = postJson(route('api.v1.administrative.users.teams.attach',
        ['user' => 'abc']));
    $response->assertUnauthorized();
})->group('administrative/user/team/detach');

it('return 404 response regular login users', function () {
    $user = UserRepository::getRandomUser();
    Sanctum::actingAs($user);

    $response = postJson(route('api.v1.administrative.users.teams.attach', ['user' => 'abc']));
    $response->assertNotFound();
})->group('administrative/user/team/detach');

test('it throws 422 error when trying to attach non existing team', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $user = UserRepository::getRandomUser();

    $response = postJson(
        route('api.v1.administrative.users.teams.attach', ['user' => $user?->hash_id]),
        ['teamIds' => [-12]]
    );

    $response->assertInvalid('teamIds.0');
})->group('administrative/user/team/detach');

test('it can attach users', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $user = User::factory()->create();
    $teams = TeamRepository::getRandomTeams(2);
    $teamIds = $teams->pluck('id')->toArray();

    $user->teams()->attach($teamIds);

    $newTeam = Team::factory()->create();
    $newTeamId = $newTeam->id;

    $attachTeamIds = collect([$newTeamId, $newTeamId, ...$teamIds, ...$teamIds])
        ->transform(fn (int $id) => Hashids::encode($id))
        ->toArray();

    $response = postJson(
        route('api.v1.administrative.users.teams.attach', ['user' => $user?->hash_id]),
        ['teamIds' => $attachTeamIds]
    );

    $response->assertCreated();

    $newTeamIds = $user->teams->fresh()->pluck('id')->toArray();

    expect($newTeamIds)
        ->toHaveCount(3)
        ->toMatchArray([...$teamIds, $newTeamId]);
})->group('administrative/user/team/attach');
