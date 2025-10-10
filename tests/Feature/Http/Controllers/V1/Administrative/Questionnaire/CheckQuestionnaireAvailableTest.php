<?php

use App\Models\Questionnaire;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\getJson;

it('return 401 response non-login users ', function () {
    $user = UserRepository::getRandomUser();

    $response = getJson(route('api.v1.administrative.questionnaires.checkAvailable',
        [
            'questionnaireId' => 'abcd',
        ]
    ));
    $response->assertUnauthorized();
})->group('administrative/questionnaires/checkAvailable');

it('can check if questionnaire is available for a user', function () {
    \Laravel\Sanctum\Sanctum::actingAs(UserRepository::getRandomUser(\App\Enums\Role::ADMIN));

    $questionnaire = Questionnaire::query()
        ->withCount('questions')
        ->completed(true)
        ->first();

    $response = getJson(route('api.v1.administrative.questionnaires.checkAvailable',
        [
            'questionnaireId' => $questionnaire?->hash_id,
        ]
    ));

    $response->assertOk();
    $response->assertJsonPath('available', true);
})->group('administrative/questionnaires/checkAvailable');
