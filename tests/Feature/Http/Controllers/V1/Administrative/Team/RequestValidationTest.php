<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\TeamRequest;

use function Pest\Laravel\postJson;

it('required name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.teams.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['name']);
})->group('api/v1/administrative/team/validation');

it('required unique name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $name = \Illuminate\Support\Str::random();

    TeamRequest::new(['name' => $name])->fake();
    $response = postJson(route('api.v1.administrative.teams.store'));
    $response->assertCreated();

    TeamRequest::new(['name' => $name])->fake();
    $response = postJson(route('api.v1.administrative.teams.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['name']);
})->group('api/v1/administrative/team/validation');
