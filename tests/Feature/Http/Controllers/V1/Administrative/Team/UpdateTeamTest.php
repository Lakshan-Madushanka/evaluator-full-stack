<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\TeamRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\putJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = putJson(route('api.v1.administrative.teams.update', ['team' => 1]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/team/update');

it('allows administrative users to update team', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $team = TeamRepository::getRandomTeam();
    $name = \Illuminate\Support\Str::random();

    $response = putJson(route('api.v1.administrative.teams.update', ['team' => $team->hash_id]),
        ['name' => $name]);
    $response->assertOk();

    $team->refresh();

    expect($team->name)->toBe($name);

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type',
        'data.attributes')
        ->etc()
    );
})->group('api/v1/administrative/team/update');
