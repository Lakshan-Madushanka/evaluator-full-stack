<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\QuestionRequest;

use function Pest\Laravel\postJson;

it('required text', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = postJson(route('api.v1.administrative.answers.store'));
    $response->assertStatus(422);
    $response->assertInvalid(['text']);
})->group('api/v1/administrative/answer/validation');

it('text must be unique', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $answerText = \Tests\Repositories\AnswerRepository::getRandomAnswer()->text;

    $response = postJson(route('api.v1.administrative.answers.store'), ['text' => $answerText]);
    $response->assertStatus(422);
    $response->assertInvalid(['text']);
})->fakeRequest(fn () => QuestionRequest::new()->without('difficulty'))
    ->group('api/v1/administrative/answer/validation');
