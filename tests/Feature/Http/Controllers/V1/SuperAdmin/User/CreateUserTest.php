<?php

use App\Enums\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\RequestFactories\UserRequest;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->seeder = \Database\Seeders\UserSeeder::class;
});

it('return 401 unauthorized response for non-login users', function () {
    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertUnauthorized();
});

it('return 404 unauthorized response for admin login users', function () {
    $admin = User::factory()->create(['role' => Role::ADMIN]);
    Sanctum::actingAs($admin);

    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertNotFound();
});

it('requires name field to create a user', function () {
    $superAdmin = User::factory()->create(['role' => Role::SUPER_ADMIN]);
    Sanctum::actingAs($superAdmin);

    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertInvalid(['name']);
})->fakeRequest(fn () => UserRequest::new()->without('name'));

it('requires email field to create a user', function () {
    $superAdmin = User::factory()->create(['role' => Role::SUPER_ADMIN]);
    Sanctum::actingAs($superAdmin);

    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertInvalid(['email']);
})->fakeRequest(fn () => UserRequest::new()->without('email'));

test('provided email must be a valid one to create a user', function () {
    $superAdmin = User::factory()->create(['role' => Role::SUPER_ADMIN]);
    Sanctum::actingAs($superAdmin);

    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertInvalid(['email']);
})->fakeRequest(fn () => UserRequest::new(['email' => 'invalidemail']));

test('provided email must be unique to create a user', function () {
    $superAdmin = User::factory()->create(['role' => Role::SUPER_ADMIN]);
    Sanctum::actingAs($superAdmin);

    UserRequest::new([
        'role' => Role::REGULAR->value,
        'email' => $superAdmin->email,
    ])->without('password')->fake();

    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertInvalid(['email']);
});

it('requires role to create a user', function () {
    $superAdmin = User::factory()->create(['role' => Role::SUPER_ADMIN]);
    Sanctum::actingAs($superAdmin);

    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertInvalid(['role']);
})->fakeRequest(fn () => UserRequest::new()->without('role'));

test('provided role must be valid to create a user', function ($value) {
    $superAdmin = User::factory()->create(['role' => Role::SUPER_ADMIN]);
    Sanctum::actingAs($superAdmin);

    UserRequest::new([
        'role' => $value,
    ]);

    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertInvalid(['role']);
})->with(['a', -1, 4, 5]);

it('password is required to create an admin user', function () {
    $superAdmin = User::factory()->create(['role' => Role::SUPER_ADMIN]);
    Sanctum::actingAs($superAdmin);

    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertInvalid(['password']);
})->fakeRequest(fn () => UserRequest::new([
    'role' => Role::ADMIN->value,
])->without('password'));

test('provided password must be grater than 7 characters to create an admin user', function () {
    $superAdmin = User::factory()->create(['role' => Role::SUPER_ADMIN]);
    Sanctum::actingAs($superAdmin);

    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertInvalid(['password']);
})->fakeRequest(fn () => UserRequest::new([
    'role' => Role::ADMIN->value,
    'password' => '1234567',
]));

test('password confirmation should match to create an admin user', function () {
    $superAdmin = User::factory()->create(['role' => Role::SUPER_ADMIN]);
    Sanctum::actingAs($superAdmin);

    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertInvalid(['password']);
    $response->assertSee('confirmation');
})->fakeRequest(fn () => UserRequest::new([
    'role' => Role::ADMIN->value,
    'password' => '1234567',
    'password_confirmation' => '0000000',
]));

it('can create a regular user without a password', function () {
    $superAdmin = User::factory()->create(['role' => Role::SUPER_ADMIN]);
    Sanctum::actingAs($superAdmin);

    $noOfUsers = User::query()->count();

    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertCreated();
    assertDatabaseCount('users', $noOfUsers + 1);
})->fakeRequest(fn () => UserRequest::new([
    'role' => Role::REGULAR->value,
])->without('password'));

it('can create an admin user', function () {
    $superAdmin = User::factory()->create(['role' => Role::SUPER_ADMIN]);
    Sanctum::actingAs($superAdmin);

    $noOfUsers = User::query()->count();

    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertCreated();

    assertDatabaseCount('users', $noOfUsers + 1);

    expect(getLastCreatedUser()->role->value)->toEqual(Role::ADMIN->value);
})->fakeRequest(fn () => UserRequest::new([
    'role' => Role::ADMIN->value,
]));

it('can create a regular user', function () {
    $superAdmin = User::factory()->create(['role' => Role::SUPER_ADMIN]);
    Sanctum::actingAs($superAdmin);

    $noOfUsers = User::query()->count();

    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertCreated();

    assertDatabaseCount('users', $noOfUsers + 1);

    expect(getLastCreatedUser()->role->value)->toEqual(Role::REGULAR->value);
})->fakeRequest(fn () => UserRequest::new([
    'role' => Role::REGULAR->value,
]));

it('encrypt password when creating new user', function () {
    $superAdmin = User::factory()->create(['role' => Role::SUPER_ADMIN]);
    Sanctum::actingAs($superAdmin);

    $password = 'abcD^&123';
    UserRequest::new([
        'role' => Role::ADMIN->value,
        'password' => $password,
        'password_confirmation' => $password,
    ])->fake();

    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertCreated();

    expect(\Illuminate\Support\Facades\Hash::check($password, getLastCreatedUser()->password))->toBeTrue();
});

it('return a response containing information about created user', function () {
    $superAdmin = User::factory()->create(['role' => Role::SUPER_ADMIN]);
    Sanctum::actingAs($superAdmin);

    $noOfUsers = User::query()->count();

    $response = postJson(route('api.v1.super-admin.users.store'));
    $response->assertCreated();
    assertDatabaseCount('users', $noOfUsers + 1);

    $response->assertJsonPath('data.id', getLastCreatedUser()->hash_id);
})->fakeRequest(fn () => UserRequest::new([
    'role' => Role::ADMIN->value,
]));

it('cannot create a super admin user', function () {
    $superAdmin = User::factory()->create(['role' => Role::SUPER_ADMIN]);
    Sanctum::actingAs($superAdmin);

    $response = postJson(route('api.v1.super-admin.users.store'));

    $response->assertInvalid(['role']);
})->fakeRequest(fn () => UserRequest::new([
    'role' => Role::SUPER_ADMIN->value,
]));

function getLastCreatedUser(): User
{
    return User::query()
        ->orderByDesc('id')
        ->first()
        ->makeVisible('password');
}
