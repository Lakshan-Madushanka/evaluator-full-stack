<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\AnswerRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\deleteJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = deleteJson(route('api.v1.administrative.answers.delete', ['answer' => 1]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/answer/delete');

it('allows super admin users to delete answer', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $answer = AnswerRepository::getRandomAnswer();
    $answersCount = AnswerRepository::getTotalAnswersCount();

    $response = deleteJson(route('api.v1.administrative.answers.delete', ['answer' => $answer->hash_id]));
    $response->assertNoContent();

    assertDatabaseCount('answers', $answersCount - 1);
})->group('api/v1/administrative/answer/delete');
