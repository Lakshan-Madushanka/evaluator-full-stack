<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\QuestionnaireRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\deleteJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = deleteJson(route('api.v1.administrative.questionnaires.delete', ['questionnaire' => 1]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/questionnaire/delete');

it('allows super admin users to delete questionnaire', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $questionnaire = QuestionnaireRepository::getRandomQuestionnaire();
    $questionnairesCount = QuestionnaireRepository::getTotalQuestionnairesCount();

    $response = deleteJson(route('api.v1.administrative.questionnaires.delete', ['questionnaire' => $questionnaire->hash_id]));
    $response->assertNoContent();

    assertDatabaseCount('questionnaires', $questionnairesCount - 1);
})->group('api/v1/administrative/questionnaire/delete');
