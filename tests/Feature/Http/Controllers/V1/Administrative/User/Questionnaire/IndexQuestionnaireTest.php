<?php

use App\Enums\Role;
use App\Models\User;
use App\Models\UserQuestionnaire;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\getJson;

it('return 401 response non-login users ', function () {
    $user = UserRepository::getRandomUser();

    $response = getJson(route('api.v1.administrative.users.questionnaires.index', ['user' => $user->hash_id]));
    $response->assertUnauthorized();
})->group('administrative/users/questionnaires/index');

it('return 404 response regular login users', function () {
    $user = UserRepository::getRandomUser();
    Sanctum::actingAs($user);

    $response = getJson(route('api.v1.administrative.users.questionnaires.index', ['user' => $user->hash_id]));
    $response->assertNotFound();
})->group('administrative/users/questionnaires/index');

test('admin can obtain all user questionnaires', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $user = UserRepository::getRandomUser();

    $user = User::findOrFail(52);

    DB::enableQueryLog();
    $response = getJson(route('api.v1.administrative.users.questionnaires.index', ['user' => $user->hash_id]));

    dd($response->json('data'));
    dd(DB::getQueryLog());
    $response->assertOk();
})->group('administrative/users/questionnaires/index');

test('can paginate user records', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $user = User::whereHas('questionnaires')->first();

    $query = '?'.http_build_query(['page' => ['size' => 1]]);

    $route = route('api.v1.administrative.users.questionnaires.index', ['user' => $user?->hash_id]).$query;
    $response = getJson($route);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', 1)
        ->hasAll(['links', 'meta', 'meta.current_page'])
        ->missing('data.0.attributes.password')
        ->etc());

    $response->assertJsonPath('meta.per_page', 1);
})->group('administrative/users/questionnaires/index');

test('can filter records by expired status', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $user = User::whereHas('questionnaires')->first();

    $query = '?'.http_build_query([
        'filter' => ['expired' => false],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $route = route('api.v1.administrative.users.questionnaires.index', ['user' => $user?->hash_id]).$query;
    $response = getJson($route);

    $results = $response->decodeResponseJson()['data'];
    $expiredProperties = collect($results)->pluck('attributes.expires_at');

    $expiredProperties->each(function ($expiredAt) {
        expect(\Carbon\Carbon::parse($expiredAt)->gte(now()))->toBeTrue();
    });
    $response->assertOk();
})->group('administrative/users/questionnaires/index');

test('can filter records by user_questionnaire_id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $user = User::whereHas('questionnaires')->first();

    $questionnaire = $user->questionnaires->first();

    $uq = UserQuestionnaire::query()
        ->where('user_id', $questionnaire->pivot['user_id'])
        ->where('questionnaire_id', $questionnaire->pivot['questionnaire_id'])
        ->first();

    $hashedUqId = $uq->hash_id;

    $query = '?'.http_build_query([
        'filter' => ['uq_id' => $hashedUqId],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $route = route('api.v1.administrative.users.questionnaires.index', ['user' => $user?->hash_id]).$query;
    $response = getJson($route);

    $results = $response->decodeResponseJson()['data'];
    $attributes = collect($results)->pluck('attributes');

    $attributes->each(function ($attr) use ($hashedUqId) {
        expect($attr['user_questionnaire_id'])->toBe($hashedUqId);
    });
    $response->assertOk();
})->group('administrative/users/questionnaires/index');

test('can filter records by questionnaire id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $user = User::whereHas('questionnaires')->first();

    $questionnaire = $user->questionnaires->first();

    $query = '?'.http_build_query([
        'filter' => ['id' => $questionnaire->hash_id],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $route = route('api.v1.administrative.users.questionnaires.index', ['user' => $user?->hash_id]).$query;
    $response = getJson($route);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    foreach ($results as $result) {
        expect($result['id'])->toBe($questionnaire->hash_id);
    }
})->group('administrative/users/questionnaires/index');
