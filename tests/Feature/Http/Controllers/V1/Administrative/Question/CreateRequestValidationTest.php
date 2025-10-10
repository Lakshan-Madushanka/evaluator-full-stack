<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\QuestionRequest;

use function Pest\Laravel\postJson;

it('required difficulty', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questions.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['difficulty']);
})->fakeRequest(fn () => QuestionRequest::new()->without('difficulty'))
    ->group('api/v1/administrative/question/validation');

it('required valid difficulty', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questions.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['difficulty']);
})->fakeRequest(fn () => QuestionRequest::new(['difficulty' => -10000]))
    ->group('api/v1/administrative/question/validation');

it('required text field', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questions.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['text']);
})->fakeRequest(fn () => QuestionRequest::new()->without(['text']))
    ->group('api/v1/administrative/question/validation');

it('required text field length greater than 2 characters', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questions.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['text']);
})->fakeRequest(fn () => QuestionRequest::new(['text' => 'ab']))
    ->group('api/v1/administrative/question/validation');

it('required no_of_answers field', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questions.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['no_of_answers']);
})->fakeRequest(fn () => QuestionRequest::new()->without(['no_of_answers']))
    ->group('api/v1/administrative/question/validation');

it('required no_of_answers field to be integer', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questions.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['no_of_answers']);
})->fakeRequest(fn () => QuestionRequest::new(['no_of_answers' => 'abc']))
    ->group('api/v1/administrative/question/validation');

it('required no_of_answers field to be minimum 2', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questions.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['no_of_answers']);
})->fakeRequest(fn () => QuestionRequest::new(['no_of_answers' => 1]))
    ->group('api/v1/administrative/question/validation');

/*it('required uploaded image size not exceed maximum allowed size', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.questions.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['images.0']);
})->fakeRequest(fn() => QuestionRequest::new()->withOverSizeImage())
    ->group('api/v1/administrative/question/validation');*/
