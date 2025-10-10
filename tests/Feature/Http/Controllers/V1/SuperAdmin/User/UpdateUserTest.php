<?php

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\UserRequest;

use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->seeder = UserSeeder::class;
});

it('return 401 unauthorized response for non-login users', function () {
    $route = route('api.v1.super-admin.users.update', UserRepository::getRandomUser(Role::REGULAR)->hash_id);
    $response = putJson($route);
    $response->assertUnauthorized();
})->group('SuperAdmin/User/Update');

it('return 404 unauthorized response for admin login users', function () {
    $admin = User::factory()->create(['role' => Role::ADMIN]);
    Sanctum::actingAs($admin);

    $route = route('api.v1.super-admin.users.update', UserRepository::getRandomUser(Role::REGULAR)->hash_id);
    $response = putJson($route);
    $response->assertNotFound();
})->group('SuperAdmin/User/Update');

it('return 404 when try to update record using primary id', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $route = route('api.v1.super-admin.users.update', UserRepository::getRandomUser(Role::REGULAR)->id);
    $response = putJson($route);
    $response->assertNotFound();
})->group('SuperAdmin/User/Update');

it('return 403 unauthorized response when try to update super admin record', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $route = route('api.v1.super-admin.users.update', ['user' => $superAdmin->hash_id]);
    $response = putJson($route);
    $response->assertForbidden();
})->fakeRequest(UserRequest::class)
    ->group('SuperAdmin/User/Update');

it('allow super admin to update record', function (Role $role) {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $user = UserRepository::getRandomUser($role)->setVisible(['password']);

    $data = UserRequest::new(['email' => $user->email])->getFactoryData()->getRequestedData();

    $route = route('api.v1.super-admin.users.update', ['user' => $user->hash_id]);
    $response = putJson($route, $data);
    $response->assertOk();

    $user->refresh();

    expect($user->name)->toEqual($data['name']);
    expect($user->email)->toEqual($data['email']);
    expect(\Illuminate\Support\Facades\Hash::check($data['password'], $user->password))->toBeTrue();
})->with([Role::ADMIN, Role::REGULAR])
    ->group('SuperAdmin/User/Update');

it('allow super admin to update record without changing email', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $user = UserRepository::getRandomUser(Role::REGULAR)->setVisible(['password']);

    $data = UserRequest::new(['email' => $user->email])->getFactoryData()->getRequestedData();

    $route = route('api.v1.super-admin.users.update', ['user' => $user->hash_id]);
    $response = putJson($route, $data);
    $response->assertOk();

    $user->refresh();

    expect($user->name)->toEqual($data['name']);
    expect($user->email)->toEqual($data['email']);
    expect(\Illuminate\Support\Facades\Hash::check($data['password'], $user->password))->toBeTrue();
})->group('SuperAdmin/User/Update');

it('allow super admin to update record without password', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $user = UserRepository::getRandomUser(Role::REGULAR);
    $route = route('api.v1.super-admin.users.update', ['user' => $user->hash_id]);

    $response = putJson($route);
    $response->assertOk();
})->fakeRequest(fn () => UserRequest::new()->without('password'))
    ->group('SuperAdmin/User/Update');
