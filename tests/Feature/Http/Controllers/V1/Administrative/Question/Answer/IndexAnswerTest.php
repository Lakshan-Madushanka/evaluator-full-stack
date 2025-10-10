<?php

use App\Enums\Role;
use App\Helpers;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\QuestionRepository;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\QuestionRequest;

it('return 401 unauthorized response for non-login users', function () {
    $response = \Pest\Laravel\getJson(route('api.v1.administrative.questions.answers.index', ['question' => 'abcd']));

    $response->assertUnauthorized();
})->group('api/v1/administrative/question/answer/index');

it('allows administrative users to get answers of a question', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $question = QuestionRepository::getRandomQuestion();

    $answersIds = $question->answers->pluck('id')->all();

    $response = \Pest\Laravel\getJson(
        route('api.v1.administrative.questions.answers.index', ['question' => $question->hash_id]),
    );
    $response->assertOk();

    $responseData = $response->decodeResponseJson()['data'];

    $responseDataIds = collect($responseData)->pluck('id')->all();

    expect($answersIds)->toBe(Helpers::getModelIdsFromHashIds($responseDataIds));

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', count($answersIds))->etc());
})->fakeRequest(QuestionRequest::class)
    ->group('api/v1/administrative/question/answer/index');
