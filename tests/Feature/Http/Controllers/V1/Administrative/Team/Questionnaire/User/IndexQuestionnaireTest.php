<?php

use App\Enums\Role;
use App\Models\Evaluation;
use App\Models\User;
use App\Models\UserQuestionnaire;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\TeamRepository;
use Tests\Repositories\UserRepository;
use Vinkla\Hashids\Facades\Hashids;

use function Pest\Laravel\getJson;

it('return 401 response non-login users ', function () {
    $response = getJson(route('api.v1.administrative.teams.questionnaires.users.index', ['questionnaireTeam' => 'abc']));
    $response->assertUnauthorized();
})->group('administrative/team/questionnaire/user/index');

it('return 404 response regular login users', function () {
    $user = UserRepository::getRandomUser();
    Sanctum::actingAs($user);

    $response = getJson(route('api.v1.administrative.teams.questionnaires.users.index', ['questionnaireTeam' => 'abc']));
    $response->assertNotFound();
})->group('administrative/team/questionnaire/user/index');

test('admin can obtain all team questionnaires', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $team = TeamRepository::createTeamsWithQuestionnaires(10);

    $teamQuestionnaireId = $team->questionnaires->first()->pivot->id;
    $hashedTeamQuestionnaireId = Hashids::encode($teamQuestionnaireId);

    $response = getJson(route('api.v1.administrative.teams.questionnaires.users.index', ['questionnaireTeam' => $hashedTeamQuestionnaireId]));
    $response->assertOk();

    $data = $response->json('data');

    expect($data)->toHaveCount(10);

    foreach ($data as $d) {
        expect($d['attributes']['user_questionnaire_team_id'])->toBe($hashedTeamQuestionnaireId);
    }
})->group('administrative/team/questionnaire/user/index');

test('admin can obtain all team questionnaires with evaluations', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $team = TeamRepository::createTeamsWithQuestionnaires(1);

    $teamQuestionnaireId = $team->questionnaires->first()->pivot->id;
    $hashedTeamQuestionnaireId = Hashids::encode($teamQuestionnaireId);

    Evaluation::create([
        'user_questionnaire_id' => $team->userQuestionnaires->first()->id,
        'time_taken' => 03,
        'correct_answers' => 25,
        'no_of_answered_questions' => 25,
        'marks_percentage' => 100,
        'total_points_earned' => 100,
        'total_points_allocated' => 100,
    ]);

    $query = '?'.http_build_query([
        'include' => 'evaluation',
    ]);

    $response = getJson(route('api.v1.administrative.teams.questionnaires.users.index', ['questionnaireTeam' => $hashedTeamQuestionnaireId]).$query);
    $response->assertOk();

    $evaluationId = $response->json('data')[0]['relationships']['evaluation']['data']['id'];

    expect($response->json('included')[0]['id'])->toBe($evaluationId)
        ->and($response->json('included')[0]['type'])->toBe('evaluations')
        ->and($response->json('included')[0]['attributes']['marks_percentage'])->toBe(100);
})->group('administrative/team/questionnaire/user/index');

test('it sorts data by marks by default', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $team = TeamRepository::createTeamsWithQuestionnaires(3);

    $teamQuestionnaireId = $team->questionnaires->first()->pivot->id;
    $hashedTeamQuestionnaireId = Hashids::encode($teamQuestionnaireId);

    Evaluation::create([
        'user_questionnaire_id' => $team->userQuestionnaires->first()->id,
        'time_taken' => 03,
        'correct_answers' => 25,
        'no_of_answered_questions' => 25,
        'marks_percentage' => 25,
        'total_points_earned' => 100,
        'total_points_allocated' => 100,
    ]);

    Evaluation::create([
        'user_questionnaire_id' => $team->userQuestionnaires[1]->id,
        'time_taken' => 03,
        'correct_answers' => 25,
        'no_of_answered_questions' => 25,
        'marks_percentage' => 100,
        'total_points_earned' => 100,
        'total_points_allocated' => 100,
    ]);

    Evaluation::create([
        'user_questionnaire_id' => $team->userQuestionnaires[2]->id,
        'time_taken' => 03,
        'correct_answers' => 25,
        'no_of_answered_questions' => 25,
        'marks_percentage' => 50,
        'total_points_earned' => 100,
        'total_points_allocated' => 100,
    ]);

    $query = '?'.http_build_query([
        'include' => 'evaluation',
    ]);

    $response = getJson(route('api.v1.administrative.teams.questionnaires.users.index', ['questionnaireTeam' => $hashedTeamQuestionnaireId]).$query);
    $response->assertOk();

    $data = $response->json('data');

    expect($data[0]['id'])->toBe($team->userQuestionnaires[1]->hash_id);
    expect($data[1]['id'])->toBe($team->userQuestionnaires[2]->hash_id);
    expect($data[2]['id'])->toBe($team->userQuestionnaires[0]->hash_id);

})->group('administrative/team/questionnaire/user/index');

test('can paginate user records', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $team = TeamRepository::createTeamsWithQuestionnaires(4);

    $teamQuestionnaireId = $team->questionnaires->first()->pivot->id;
    $hashedTeamQuestionnaireId = Hashids::encode($teamQuestionnaireId);

    $query = '?'.http_build_query(['page' => ['size' => 3]]);

    $route = route('api.v1.administrative.teams.questionnaires.users.index', ['questionnaireTeam' => $hashedTeamQuestionnaireId]).$query;
    $response = getJson($route);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', 3)
        ->hasAll(['links', 'meta', 'meta.current_page'])
        ->etc());

    $response->assertJsonPath('meta.per_page', 3);
})->group('administrative/team/questionnaire/user/index');

test('can filter records by expired status', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $team = TeamRepository::createTeamsWithQuestionnaires(4);

    $teamQuestionnaireId = $team->questionnaires->first()->pivot->id;
    $hashedTeamQuestionnaireId = Hashids::encode($teamQuestionnaireId);

    $query = '?'.http_build_query([
        'filter' => ['expired' => false],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $route = route('api.v1.administrative.teams.questionnaires.users.index', ['questionnaireTeam' => $hashedTeamQuestionnaireId]).$query;
    $response = getJson($route);

    $results = $response->decodeResponseJson()['data'];
    $expiredProperties = collect($results)->pluck('attributes.expires_at');

    $expiredProperties->each(function ($expiredAt) {
        expect(Carbon::parse($expiredAt)->gte(now()))->toBeTrue();
    });
    $response->assertOk();
})->group('administrative/team/questionnaire/user/index');

test('can filter records by user name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $team = TeamRepository::createTeamsWithQuestionnaires(4);

    $teamQuestionnaireId = $team->questionnaires->first()->pivot->id;
    $hashedTeamQuestionnaireId = Hashids::encode($teamQuestionnaireId);

    $name = $team->users->first()->name;

    $query = '?'.http_build_query([
        'filter' => ['name' => $name],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $route = route('api.v1.administrative.teams.questionnaires.users.index', ['questionnaireTeam' => $hashedTeamQuestionnaireId]).$query;
    $response = getJson($route);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    foreach ($results as $result) {
        $userId = UserQuestionnaire::find(Hashids::decode($result['id'])[0])->user_id;
        $uName = User::find($userId)->name;

        expect(Str::contains($uName, $uName))->toBeTrue();
    }
})->group('administrative/team/questionnaire/user/index');

test('can filter records by user email', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $team = TeamRepository::createTeamsWithQuestionnaires(4);

    $teamQuestionnaireId = $team->questionnaires->first()->pivot->id;
    $hashedTeamQuestionnaireId = Hashids::encode($teamQuestionnaireId);

    $email = $team->users->first()->email;

    $query = '?'.http_build_query([
        'filter' => ['name' => $email],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $route = route('api.v1.administrative.teams.questionnaires.users.index', ['questionnaireTeam' => $hashedTeamQuestionnaireId]).$query;
    $response = getJson($route);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    foreach ($results as $result) {
        $userId = UserQuestionnaire::find(Hashids::decode($result['id'])[0])->user_id;
        $uEmail = User::find($userId)->email;

        expect(Str::contains($uEmail, $email))->toBeTrue();
    }
})->group('administrative/team/questionnaire/user/index');
