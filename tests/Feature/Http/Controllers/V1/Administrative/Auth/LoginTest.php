<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'role' => Role::ADMIN->value,
    ]);
});

it('needs email to login user', function () {
    $response = sendLoginRequest(['password' => 'password']);

    $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertInvalid(['email']);
})->group('administrative/users/login');

it('needs password to login', function () {
    $response = sendLoginRequest(['email' => $this->admin['email']]);

    $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertInvalid(['password']);
})->group('administrative/users/login');

it('need valid email to login', function () {
    $response = sendLoginRequest([
        'email' => \Illuminate\Support\Str::random(),
        'password' => 'password',
    ]);

    $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertInvalid(['email']);
})->group('administrative/users/login');

it('needs valid credentials[email] to login', function () {
    $response = sendLoginRequest([
        'email' => \Illuminate\Support\Str::random().'@'.'mail.com',
        'password' => 'password',
    ]);

    $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertInvalid(['email']);
})->group('administrative/users/login');

it('needs valid credentials[password] to login', function () {
    $response = sendLoginRequest([
        'email' => \Illuminate\Support\Str::random().'@'.'mail.com',
        'password' => \Illuminate\Support\Str::random(),
    ]);

    $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertInvalid(['email']);
})->group('administrative/users/login');

it('cannot login regular users', function () {
    $user = User::factory()->create([
        'password' => 'password',
        'role' => Role::REGULAR->value,
    ])->makeVisible('password');

    $response = sendLoginRequest([
        'email' => $user->email,
        'password' => $user->password,
    ]);

    $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertInvalid(['email']);
})->group('administrative/users/login');

it('can login admin users', function () {
    $response = sendLoginRequest(['email' => $this->admin['email'], 'password' => 'password']);

    $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    $response->assertOk();

    assertAuthenticated();
})->group('administrative/users/login');

it('login users receive redirect response when try to re-login', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));
    $response = sendLoginRequest(['email' => $this->admin['email'], 'password' => 'password']);

    $response->assertRedirect();
})->group('administrative/users/login');

it('can login super admin users', function () {
    $user = User::factory()->create(['role' => Role::SUPER_ADMIN->value]);

    $response = sendLoginRequest(['email' => $user->email, 'password' => 'password']);

    $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    $response->assertOk();

    assertAuthenticated();
})->group('administrative/users/login');

it('return login user info after login', function () {
    $user = User::factory()->create(['role' => Role::SUPER_ADMIN->value]);

    $response = sendLoginRequest(['email' => $user->email, 'password' => 'password']);

    $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    $response->assertOk();

    assertAuthenticated();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data')
        ->has('data.attributes')
        ->where('data.attributes.email', $user->email)
        ->etc()
    );
})->group('administrative/users/login');

it('can show login user info', function () {
    $user = User::factory()->create(['role' => Role::SUPER_ADMIN->value]);

    $response = sendLoginRequest(['email' => $user->email, 'password' => 'password']);

    $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    $response->assertOk();

    assertAuthenticated();

    $response2 = getJson(route('api.v1.administrative.user'));

    $response2->assertJson(fn (AssertableJson $json) => $json->has('data')
        ->has('data.attributes')
        ->where('data.attributes.email', $user->email)
        ->etc()
    );
})->group('administrative/users/login');

it('can throttle failed login attempts', function () {
    $payload = [
        'email' => Str::random().'@mail.com',
        'password' => Str::random(),
    ];

    $i = 1;
    while ($i !== 6) {
        sendLoginRequest($payload);
        $i++;
    }

    // sending 6th response which receive throttle exception
    try {
        $response = sendLoginRequest($payload);
    } catch (Exception $exception) {
        expect($exception)->toBeInstanceOf(ThrottlesExceptions::class);
    }
    $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_TOO_MANY_REQUESTS);
})->group('administrative/users/login');

function sendLoginRequest(array $data): TestResponse
{
    return postJson(
        uri: route('api.v1.administrative.login'),
        data: $data,
        headers: [
            'withCredentials' => true,
            'accept' => 'application/json',
            'origin' => 'mcq.io',
        ]
    );
}
