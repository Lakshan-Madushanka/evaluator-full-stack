<?php

use App\Enums\Role;
use Database\Seeders\UserSeeder;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\UserRequest;

use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->seeder = UserSeeder::class;
});

it('return 401 unauthorized response for non-login users', function () {
    $route = route('api.v1.administrative.profile', UserRepository::getRandomUser(Role::REGULAR)->hash_id);
    $response = putJson($route);
    $response->assertUnauthorized();
})->group('administrative/profile/update');

it('return 404 when try to update record using primary id', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $route = route('api.v1.administrative.profile', UserRepository::getRandomUser(Role::REGULAR)->id);
    $response = putJson($route);
    $response->assertNotFound();
})->group('administrative/profile/update');

it('allows super admin to update their records', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $user = UserRequest::new()->create();

    $route = route('api.v1.administrative.profile', $superAdmin->hash_id);
    $response = putJson($route, $user);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data')->etc());

    $superAdmin->refresh();

    expect($superAdmin->name)->toBe($user['name']);
    expect($superAdmin->email)->toBe($user['email']);
    expect(\Illuminate\Support\Facades\Hash::check($user['password'], $superAdmin->password))->toBeTrue();
})->group('administrative/profile/update');

it('allows admin to only update their password', function () {
    $admin = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($admin);

    $user = UserRequest::new()->create();

    $route = route('api.v1.administrative.profile', $admin->hash_id);
    $response = putJson($route, $user);
    $response->assertOk();

    $updatedAdmin = $admin->fresh();

    expect($admin->name)->toBe($updatedAdmin->name);
    expect($admin->email)->toBe($updatedAdmin->email);
    expect(\Illuminate\Support\Facades\Hash::check($user['password'], $updatedAdmin->password))->toBeTrue();
})->group('administrative/profile/update');

it('cannot update user role', function () {
    $admin = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($admin);

    $user = UserRequest::new()->create(['role' => Role::REGULAR->value]);

    $route = route('api.v1.administrative.profile', $admin->hash_id);
    $response = putJson($route, $user);
    $response->assertOk();

    $updatedAdmin = $admin->fresh();

    expect($admin->role->value)->toBe($updatedAdmin->role->value);
})->group('administrative/profile/update');
