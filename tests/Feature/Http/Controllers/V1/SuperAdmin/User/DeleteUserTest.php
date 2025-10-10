<?php

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\UserRequest;

use function Pest\Laravel\deleteJson;

beforeEach(function () {
    $this->seeder = UserSeeder::class;
});

it('return 401 unauthorized response for non-login users', function () {
    $route = route('api.v1.super-admin.users.delete', UserRepository::getRandomUser(Role::REGULAR)->hash_id);
    $response = deleteJson($route);
    $response->assertUnauthorized();
})->group('SuperAdmin/User/Delete');

it('return 404 unauthorized response for admin login users', function () {
    $admin = User::factory()->create(['role' => Role::ADMIN]);
    Sanctum::actingAs($admin);

    $route = route('api.v1.super-admin.users.delete', UserRepository::getRandomUser(Role::REGULAR)->hash_id);
    $response = deleteJson($route);
    $response->assertNotFound();
})->group('SuperAdmin/User/Delete');

it('return 404 when try to delete record using primary id', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $route = route('api.v1.super-admin.users.delete', UserRepository::getRandomUser()->id);
    $response = deleteJson($route);
    $response->assertNotFound();
})->group('SuperAdmin/User/Delete');

it('return 403 unauthorized response when try to delete super admin record', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $route = route('api.v1.super-admin.users.delete', ['user' => $superAdmin->hash_id]);
    $response = deleteJson($route);
    $response->assertForbidden();
})->fakeRequest(UserRequest::class)
    ->group('SuperAdmin/User/Delete');

it('allow super admin to delete a user', function (Role $role) {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $user = UserRepository::getRandomUser($role);

    $route = route('api.v1.super-admin.users.delete', [
        'user' => $user->hash_id,
    ]);
    $response = deleteJson($route);
    $response->assertNoContent();

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
})->with([Role::ADMIN, Role::REGULAR])
    ->fakeRequest(UserRequest::class)
    ->group('SuperAdmin/User/Delete');
