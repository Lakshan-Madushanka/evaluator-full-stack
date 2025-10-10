<?php

use App\Enums\Role;
use App\Models\Questionnaire;
use App\Models\UserQuestionnaire;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\postJson;

it('return 401 response non-login users ', function () {
    $user = UserRepository::getRandomUser();

    $response = deleteJson(route('api.v1.administrative.users.questionnaires.detach',
        ['user' => $user->hash_id, 'userQuestionnaireId' => 'abc']));
    $response->assertUnauthorized();
})->group('administrative/users/questionnaires/detach');

it('return 404 response regular login users', function () {
    $user = UserRepository::getRandomUser();
    Sanctum::actingAs($user);

    $response = deleteJson(route('api.v1.administrative.users.questionnaires.detach',
        ['user' => $user->hash_id, 'userQuestionnaireId' => 'abc']));
    $response->assertNotFound();
})->group('administrative/users/questionnaires/detach');

test('it throws 422 error when trying to detach already attempted questionnaire', function () {
    // First we attach a questionnaire to a user
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $user = UserRepository::getRandomUser();

    $questionnaire = Questionnaire::query()
        ->withCount('questions')
        ->completed(value: true)
        ->first();

    $response1 = postJson(
        route('api.v1.administrative.users.questionnaires.attach', [
            'user' => $user?->hash_id,
            'questionnaireId' => $questionnaire->hash_id,
        ])
    );

    $response1->assertOk();

    $attachedQuestionnaire = UserQuestionnaire::query()
        ->where([
            ['user_id', $user->id],
            ['questionnaire_id', $questionnaire->id],
        ])->first();

    $attachedQuestionnaire->attempts = 1;
    $attachedQuestionnaire->save();

    $response1 = deleteJson(
        route('api.v1.administrative.users.questionnaires.detach', [
            'user' => $user?->hash_id,
            'userQuestionnaireId' => $attachedQuestionnaire->hash_id,
        ])
    );

    $response1->assertUnprocessable();
})->group('administrative/users/questionnaires/detach');

test('allows to detach a active questionnaire', function () {
    // First we attach a questionnaire to a user
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $user = UserRepository::getRandomUser();

    $questionnaire = Questionnaire::query()
        ->withCount('questions')
        ->completed(value: true)
        ->first();

    $response1 = postJson(
        route('api.v1.administrative.users.questionnaires.attach', [
            'user' => $user?->hash_id,
            'questionnaireId' => $questionnaire->hash_id,
        ])
    );

    $response1->assertOk();

    $attachedQuestionnaire = UserQuestionnaire::query()
        ->where([
            ['user_id', $user->id],
            ['questionnaire_id', $questionnaire->id],
        ])->first();

    $response1 = deleteJson(
        route('api.v1.administrative.users.questionnaires.detach', [
            'user' => $user?->hash_id,
            'userQuestionnaireId' => $attachedQuestionnaire->hash_id,
        ])
    );

    $response1->assertok();

    $questionnaire = $attachedQuestionnaire->fresh();

    expect($questionnaire)->toBeNull();
})->group('administrative/users/questionnaires/detach');
