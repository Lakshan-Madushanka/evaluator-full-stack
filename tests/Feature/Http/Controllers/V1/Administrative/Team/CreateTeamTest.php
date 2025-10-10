<?php

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\TeamRepository;
use Tests\RequestFactories\TeamRequest;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->seeder = UserSeeder::class;
});

it('return 401 unauthorized response for non-login users', function () {
    $response = postJson(route('api.v1.administrative.teams.store'));
    $response->assertUnauthorized();
})->group('api/v1/administrative/team/store');

it('requires name field to create a team', function () {
    $admin = User::factory()->create(['role' => Role::ADMIN]);
    Sanctum::actingAs($admin);

    $response = postJson(route('api.v1.administrative.teams.store'));
    $response->assertUnprocessable();
    $response->assertInvalid(['name']);

})->fakeRequest(fn () => TeamRequest::new()->without('name'))
    ->group('api/v1/administrative/team/store');

it('can create a team', function () {
    $admin = User::factory()->create(['role' => Role::ADMIN]);
    Sanctum::actingAs($admin);

    $teamsCount = TeamRepository::getTotalTeamsCount();

    $response = postJson(route('api.v1.administrative.teams.store'));
    $response->assertCreated();

    assertDatabaseCount('teams', $teamsCount + 1);

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type',
        'data.attributes')
        ->etc()
    );
})->fakeRequest(fn () => TeamRequest::new())
    ->group('api/v1/administrative/team/store');
