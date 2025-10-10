<?php

use App\Enums\Role;
use App\Models\Questionnaire;
use App\Models\Team;
use App\Models\User;
use App\Models\UserQuestionnaire;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\TeamRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\postJson;

it('return 401 response non-login users ', function () {
    $response = postJson(route('api.v1.administrative.teams.questionnaires.attach',
        [
            'team' => 'abc',
            'questionnaireId' => 'abc',
        ],
    ));
    $response->assertUnauthorized();
})->group('administrative/team/questionnaire/attach');

it('return 404 response regular login users', function () {
    $user = UserRepository::getRandomUser();
    Sanctum::actingAs($user);

    $response = postJson(route('api.v1.administrative.teams.questionnaires.attach',
        [
            'team' => 'abc',
            'questionnaireId' => 'abc',
        ],
    ));
    $response->assertNotFound();
})->group('administrative/team/questionnaire/attach');

test('return eligible false for uncompleted questionnaire', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $team = TeamRepository::getRandomTeam();

    $questionnaire = Questionnaire::factory()->create();

    $response = postJson(route(
        'api.v1.administrative.teams.questionnaires.attach', [
            'team' => $team->hash_id,
            'questionnaireId' => $questionnaire->hash_id,
        ])
    );

    $response->assertJsonPath('eligible', false);
})->group('administrative/users/questionnaires/attach');

test('allows attach eligible questionnaire to a team', function () {
    \Illuminate\Support\Facades\Notification::fake();

    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $team = Team::factory()
        ->has(User::factory()->count(2))
        ->create();

    $questionnaire = Questionnaire::query()
        ->withCount('questions')
        ->completed(value: true)
        ->first();

    $response = postJson(
        route('api.v1.administrative.teams.questionnaires.attach', [
            'team' => $team?->hash_id,
            'questionnaireId' => $questionnaire->hash_id,
        ])
    );

    $response->assertOk();

    $tqs = $team->questionnaires()->withPivot(['id'])->get();

    expect($tqs)
        ->toHaveCount(1)
        ->and($tqs[0]->pivot['team_id'])->toBe($team->id)
        ->and($tqs[0]->pivot['questionnaire_id'])->toBe($questionnaire->id);

    $uqs = UserQuestionnaire::query()
        ->where('questionnaire_team_id', $tqs->first()->pivot['id'])
        ->get();

    expect($uqs)
        ->toHaveCount(2);

    foreach ($uqs as $uq) {
        expect($uq->user_id)->toBeIn($team->users->pluck('id')->toArray())
            ->and($uq->questionnaire_id)->toBe($questionnaire->id);
    }
})->group('administrative/team/questionnaire/attach');
