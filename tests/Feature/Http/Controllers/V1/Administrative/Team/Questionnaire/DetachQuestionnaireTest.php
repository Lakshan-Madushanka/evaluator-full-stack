<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\TeamRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\deleteJson;

it('return 401 response non-login users ', function () {
    $response = deleteJson(route('api.v1.administrative.teams.questionnaires.detach', [
            'team' => 'abc',
            'questionnaire' => 'abc'
        ])
    );
    $response->assertUnauthorized();
})->group('administrative/team/questionnaire/detach');

it('return 404 response regular login users', function () {
    $user = UserRepository::getRandomUser();
    Sanctum::actingAs($user);

    $response = deleteJson(route('api.v1.administrative.teams.questionnaires.detach', [
            'team' => 'abc',
            'questionnaire' => 'abc'
        ])
    );
    $response->assertNotFound();
})->group('administrative/team/questionnaire/detach');

test('it throws 422 error when trying to detach already attempted questionnaire', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $team = TeamRepository::createTeamsWithQuestionnaires();

    $questionnaire = $team
        ->userQuestionnaires
        ->firstOrFail();

    $questionnaire->attempts = 1;
    $questionnaire->save();


    $response = deleteJson(
        route('api.v1.administrative.teams.questionnaires.detach', [
            'team' => $team?->hash_id,
            'questionnaire' => $team->questionnaires->first()->hash_id,
        ])
    );

    $response->assertUnprocessable();

    expect($team->questionnaires)->not->toBeEmpty();
})->group('administrative/team/questionnaire/detach');

test('it non attempted questionnaire', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $team = TeamRepository::createTeamsWithQuestionnaires();

    $response = deleteJson(
        route('api.v1.administrative.teams.questionnaires.detach', [
            'team' => $team?->hash_id,
            'questionnaire' => $team->questionnaires->first()->hash_id,
        ])
    );

    $response->assertNoContent();

    expect($team->questionnaires()->get())->not->toBeTrue();

})->group('administrative/team/questionnaire/detach');
