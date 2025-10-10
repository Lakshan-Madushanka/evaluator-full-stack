<?php

use App\Enums\Role;
use Database\Seeders\QuestionnaireSeeder;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\QuestionnaireRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->seeder = QuestionnaireSeeder::class;
});

it('return 401 unauthorized response for non-login questionnaire', function () {
    $route = route('api.v1.administrative.questionnaires.mass-delete');
    $response = deleteJson($route);
    $response->assertUnauthorized();
})->group('api/v1/administrative/questionnaire/mass-delete');

it('requires ids field to delete records', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $route = route('api.v1.administrative.questionnaires.mass-delete');

    $response = postJson($route);
    $response->assertInvalid(['ids']);
})->group('api/v1/administrative/questionnaire/mass-delete');

it('allows administrative questionnaire to mass delete questionnaire', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $questionnaire = QuestionnaireRepository::getRandomQuestionnaires();
    $hashIds = $questionnaire->pluck('hash_id');

    $route = route('api.v1.administrative.questionnaires.mass-delete');

    $response = postJson($route, ['ids' => $hashIds->all()]);
    $response->assertNoContent();

    $questionnaire->each(function (App\Models\Questionnaire $questionnaire) {
        assertDatabaseMissing('questionnaires', [
            'id' => $questionnaire->id,
        ]);
    });
})->group('api/v1/administrative/questionnaire/mass-delete');
