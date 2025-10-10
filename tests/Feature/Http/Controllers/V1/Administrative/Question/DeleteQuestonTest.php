<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\QuestionRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\deleteJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = deleteJson(route('api.v1.administrative.questions.delete', ['question' => 1]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/question/delete');

it('allows super admin users to delete question', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $question = QuestionRepository::getRandomQuestion();
    $questionsCount = QuestionRepository::getTotalQuestionsCount();

    $response = deleteJson(route('api.v1.administrative.questions.delete', ['question' => $question->hash_id]));
    $response->assertNoContent();

    assertDatabaseCount('questions', $questionsCount - 1);
})->group('api/v1/administrative/question/delete');
