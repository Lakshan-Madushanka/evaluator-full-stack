<?php

use App\Enums\Role;
use App\Models\Questionnaire;
use App\Models\UserQuestionnaire;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\TeamRepository;
use Tests\Repositories\UserRepository;
use Vinkla\Hashids\Facades\Hashids;

use function Pest\Laravel\getJson;

it('return 401 response non-login users ', function () {
    $user = UserRepository::getRandomUser();

    $response = getJson(route('api.v1.administrative.teams.questionnaires.index', ['team' => 'abc']));
    $response->assertUnauthorized();
})->group('administrative/team/questionnaire/index');

it('return 404 response regular login users', function () {
    $user = UserRepository::getRandomUser();
    Sanctum::actingAs($user);

    $response = getJson(route('api.v1.administrative.teams.questionnaires.index', ['team' => 'abc']));
    $response->assertNotFound();
})->group('administrative/team/questionnaires/index');

test('admin can obtain all user questionnaires', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $team = TeamRepository::createTeamsWithQuestionnaires(5);

    $team
        ->userQuestionnaires
        ->random(3)
        ->each(function (UserQuestionnaire $userQuestionnaire) {
            $userQuestionnaire->attempts = 1;
            $userQuestionnaire->save();
        });

    $response = getJson(route('api.v1.administrative.teams.questionnaires.index', ['team' => $team->hash_id]));
    $response->assertOk();

    $data = $response->json('data')[0];

    expect($data['attributes']['team_questionnaire_id'])->toBe(Hashids::encode($team->questionnaires->first()->pivot->id))
        ->and($data['attributes']['total_users'])->toBe(5)
        ->and($data['attributes']['attempted_users'])->toBe(3);
})->group('administrative/team/questionnaire/index');

test('can paginate user records', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $team = TeamRepository::createTeamsWithQuestionnaires(5);

    $query = '?'.http_build_query(['page' => ['size' => 1]]);

    $response = getJson(route('api.v1.administrative.teams.questionnaires.index', ['team' => $team->hash_id]).$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', 1)
        ->hasAll(['links', 'meta', 'meta.current_page'])
        ->etc());

    $response->assertJsonPath('meta.per_page', 1);
})->group('administrative/team/questionnaire/index');

test('can filter questionnaires by name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $team = TeamRepository::createTeamsWithQuestionnaires(5);

    $name = Str::random();

    $questionnaire = Questionnaire::factory()->create(['name' => $name]);
    $team->questionnaires()->attach([$questionnaire->id]);

    $query = '?'.http_build_query([
        'filter' => ['name' => 'test'],
        'sort' => 'created_at',
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson(route('api.v1.administrative.teams.questionnaires.index', ['team' => $team->hash_id]).$query);
    $response->assertOk();

    $data = $response->json('data');

    foreach ($data as $questionnaire) {
        expect($questionnaire['attributes']['questionnaire_name'])->toBe($name);
    }
})->group('administrative/team/questionnaire/index');
