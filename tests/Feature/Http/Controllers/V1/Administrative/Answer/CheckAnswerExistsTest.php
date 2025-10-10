<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\AnswerRepository;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\AnswerRequest;

it('return 401 unauthorized response for non-login users', function () {
    $response = \Pest\Laravel\getJson(route('api.v1.administrative.answers.checkExists', [
        'id' => \Illuminate\Support\Str::random(),
    ]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/answer/checkExists');

it('return true if answer is exists', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $answer = AnswerRepository::getRandomAnswer();

    $response = \Pest\Laravel\getJson(route('api.v1.administrative.answers.checkExists', [
        'id' => $answer->pretty_id,
    ]));
    $response->assertJsonPath('exists', true);
})->fakeRequest(AnswerRequest::class)
    ->group('api/v1/administrative/answer');

it('return true if answer is not exists', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = \Pest\Laravel\getJson(route('api.v1.administrative.answers.checkExists', [
        'id' => \Illuminate\Support\Str::random(),
    ]));
    $response->assertJsonPath('exists', false);
})->fakeRequest(AnswerRequest::class)
    ->group('api/v1/administrative/answer/checkExists');
