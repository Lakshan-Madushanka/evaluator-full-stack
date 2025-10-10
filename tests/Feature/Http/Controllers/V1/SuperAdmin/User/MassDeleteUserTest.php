<?php

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->seeder = UserSeeder::class;
});

it('return 401 unauthorized response for non-login users', function () {
    $route = route('api.v1.super-admin.users.mass-delete');
    $response = deleteJson($route);
    $response->assertUnauthorized();
})->group('SuperAdmin/User/MassDelete');

it('return 404 unauthorized response for admin login users', function () {
    $admin = User::factory()->create(['role' => Role::ADMIN]);
    Sanctum::actingAs($admin);

    $route = route('api.v1.super-admin.users.mass-delete');
    $response = deleteJson($route);
    $response->assertNotFound();
})->group('SuperAdmin/User/MassDelete');

it('requires ids field to delete records', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $route = route('api.v1.super-admin.users.mass-delete');

    $response = postJson($route);
    $response->assertInvalid(['ids']);
})->group('SuperAdmin/User/MassDelete');

it('cannot mass delete super admin users', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $users = UserRepository::getRandomUsers([Role::SUPER_ADMIN], limit: 2);
    $hashIds = $users->pluck('hash_id');

    $route = route('api.v1.super-admin.users.mass-delete');

    $response = postJson($route, ['ids' => $hashIds->all()]);
    $response->assertNoContent();

    $users->each(function (User $user) {
        assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    });
})->group('SuperAdmin/User/MassDelete');

it('can mass delete admin and regular users', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $users = UserRepository::getRandomUsers([Role::ADMIN, Role::REGULAR]);
    $hashIds = $users->pluck('hash_id');

    $route = route('api.v1.super-admin.users.mass-delete');

    $response = postJson($route, ['ids' => $hashIds->all()]);
    $response->assertNoContent();

    $users->each(function (User $user) {
        assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    });
})->group('SuperAdmin/User/MassDelete');
