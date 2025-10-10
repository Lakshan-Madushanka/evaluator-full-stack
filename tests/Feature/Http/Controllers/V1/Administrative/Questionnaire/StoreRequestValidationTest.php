<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\QuestionnaireRequest;
use Tests\RequestFactories\QuestionRequest;

use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->route = route('api.v1.administrative.questionnaires.store');
});

it('required name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson($this->route);
    $response->assertStatus(422);
    $response->assertInvalid(['name']);
})->fakeRequest(fn () => QuestionnaireRequest::new()->without('name'))
    ->group('api/v1/administrative/questionnaire/validation');

it('required name to be minimum 3 characters', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson($this->route);
    $response->assertStatus(422);
    $response->assertInvalid(['name']);
})->fakeRequest(fn () => QuestionnaireRequest::new(['name' => '#1']))
    ->group('api/v1/administrative/questionnaire/validation');

it('required name to be maximum 50 characters', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson($this->route);
    $response->assertStatus(422);
    $response->assertInvalid(['name']);
})->fakeRequest(fn () => QuestionnaireRequest::new([
    'name' => \Illuminate\Support\Str::random().str_repeat('0', 50),
]))->group('api/v1/administrative/questionnaire/validation');

it('required difficulty', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questionnaires.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['difficulty']);
})->fakeRequest(fn () => QuestionRequest::new()->without('difficulty'))
    ->group('api/v1/administrative/questionnaire/validation');

it('required valid difficulty', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questionnaires.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['difficulty']);
})->fakeRequest(fn () => QuestionRequest::new(['difficulty' => -10000]))
    ->group('api/v1/administrative/questionnaire/validation');

it('required no of easy questions', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questionnaires.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['no_of_easy_questions']);
})->fakeRequest(fn () => QuestionRequest::new()->without('no_of_easy_questions'))
    ->group('api/v1/administrative/questionnaire/validation');

it('required no of medium questions', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questionnaires.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['no_of_medium_questions']);
})->fakeRequest(fn () => QuestionRequest::new()->without('no_of_medium_questions'))
    ->group('api/v1/administrative/questionnaire/validation');

it('required no of hard questions', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questionnaires.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['no_of_hard_questions']);
})->fakeRequest(fn () => QuestionRequest::new()->without('no_of_hard_questions'))
    ->group('api/v1/administrative/questionnaire/validation');

it('required no of total questions', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questionnaires.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['no_of_questions']);
})->fakeRequest(fn () => QuestionRequest::new()->without('no_of_questions'))
    ->group('api/v1/administrative/questionnaire/validation');

it('can validate no of total questions', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questionnaires.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['no_of_questions']);
})->fakeRequest(fn () => QuestionRequest::new([
    'no_of_easy_questions' => 5,
    'no_of_medium_questions' => 5,
    'no_of_hard_questions' => 5,
    'no_of_questions' => 20,
]))->group('api/v1/administrative/questionnaire/validation');

it('required categories field', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questionnaires.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['categories']);
})->fakeRequest(fn () => QuestionRequest::new()->without('categories'))
    ->group('api/v1/administrative/questionnaire/validation');
