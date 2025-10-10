<?php

use App\Enums\Role;
use App\Models\Question;
use JetBrains\PhpStorm\ArrayShape;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\AnswerRepository;
use Tests\Repositories\QuestionRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\postJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = postJson(
        route('api.v1.administrative.questions.answers.async', ['question' => 'abcd']),
        ['answers' => []]
    );
    $response->assertUnauthorized();
})->group('api/v1/administrative/question/answer/async');

it('throws validation exception when try to sync no of answers greater than allowed no of answers', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $question = QuestionRepository::getRandomQuestion();

    $payload = getPayload($question->no_of_answers + 1);

    $response = postJson(
        route('api.v1.administrative.questions.answers.async', ['question' => $question->hash_id]),
        $payload
    );

    $response->assertUnprocessable();
    $response->assertInvalid(['answers']);
})->group('api/v1/administrative/question/answer/async');

it('throws validation exception when it doesnt have at least one correct answer', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $question = QuestionRepository::getRandomQuestion();

    $payload = getPayload($question->no_of_answers);

    $response = postJson(
        route('api.v1.administrative.questions.answers.async', ['question' => $question->hash_id]),
        $payload
    );

    $response->assertUnprocessable();
    $response->assertInvalid(['answers']);
})->group('api/v1/administrative/question/answer/async');

it('throws validation exception when assigning more than one correct answers for a single answers type question',
    function () {
        Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

        $question = Question::where('is_answers_type_single', true)->first();

        $payload = getPayload($question?->no_of_answers, true);

        $response = postJson(
            route('api.v1.administrative.questions.answers.async', ['question' => $question?->hash_id]),
            $payload
        );

        $response->assertUnprocessable();
        $response->assertInvalid(['answers']);
    })->group('api/v1/administrative/question/answer/async');

it('allows administrative users to async one correct answer to single type answers question', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $question = Question::where('is_answers_type_single', true)->first();

    $payload = getPayload($question?->no_of_answers, false);
    $payload['answers'][0]['correct'] = true;

    $response = postJson(
        route('api.v1.administrative.questions.answers.async', ['question' => $question?->hash_id]),
        $payload
    );
    $response->assertOk();

    $newAnswersIds = $question?->answers->pluck('id')->all();

    expect($newAnswersIds)->toBe($newAnswersIds);
})->group('api/v1/administrative/question/answer/async');

it('allows administrative users to async answers to multiple answers type question', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $question = Question::where('is_answers_type_single', false)->first();

    $payload = getPayload($question?->no_of_answers, true);

    $response = postJson(
        route('api.v1.administrative.questions.answers.async', ['question' => $question?->hash_id]),
        $payload
    );
    $response->assertOk();

    $newAnswersIds = $question?->answers->pluck('id')->all();

    expect($newAnswersIds)->toBe($newAnswersIds);
})->group('api/v1/administrative/question/answer/async');

it('allows administrative users to remove all answers of a question', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $question = QuestionRepository::getRandomQuestion();

    $payload = ['answers' => []];

    $response = postJson(
        route('api.v1.administrative.questions.answers.async', ['question' => $question->hash_id]),
        $payload
    );
    $response->assertOk();

    $newAnswersCount = $question->answers->pluck('id')->count();

    expect($newAnswersCount)->toBe(0);
})->group('api/v1/administrative/question/answer/async');

#[ArrayShape(['answers' => 'array'])] function getPayload(int $limit, bool $correctAnswer = false): array
{
    $payload = [];

    $answersIds = AnswerRepository::getRandomAnswers($limit)
        ->pluck('id')
        ->transform(fn ($id) => \Vinkla\Hashids\Facades\Hashids::encode($id))
        ->all();

    foreach ($answersIds as $answerId) {
        $payload[] = ['id' => $answerId, 'correct' => $correctAnswer];
    }

    return ['answers' => $payload];
}
