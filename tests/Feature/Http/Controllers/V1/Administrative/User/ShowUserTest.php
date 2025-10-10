<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\getJson;

it('return 401 response non-login users ', function () {
    $user = UserRepository::getRandomUser();

    $response = getJson(route('api.v1.administrative.users.show', ['user' => $user->id]));
    $response->assertUnauthorized();
})->group('administrative/users/show');

it('return 404 response regular login users ', function () {
    $user = UserRepository::getRandomUser();
    Sanctum::actingAs($user);

    $response = getJson(route('api.v1.administrative.users.show', ['user' => $user->id]));
    $response->assertNotFound();
})->group('administrative/users/show');

it('return  404 when try to obtain record by primary key', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $response = getJson(route('api.v1.administrative.users.show', ['user' => $user->id]));
    $response->assertNotFound();
})->group('administrative/users/show');

test('super admin can obtain record by its hash key', function () {
    $user = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($user);

    $response = getJson(route('api.v1.administrative.users.show', ['user' => $user->hash_id]));
    $response->assertOk();
})->group('administrative/users/show');

test('admin can obtain record by its hash key', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $response = getJson(route('api.v1.administrative.users.show', ['user' => $user->hash_id]));
    $response->assertOk();
})->group('administrative/users/show');

test('can return a correct user data in expected format ', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $response = getJson(route('api.v1.administrative.users.show', ['user' => $user->hash_id]));
    $response->assertOk();

    $response
        ->assertJson(fn (AssertableJson $json) => $json->hasAll([
            'data.attributes',
            'data.type',
            'data.id',
        ])
            ->missing('data.attributes.password')
            ->first(fn ($json) => $json->where('attributes.name', $user->name)
                ->etc()
            )
            ->etc()
        );
})->group('administrative/users/show');
