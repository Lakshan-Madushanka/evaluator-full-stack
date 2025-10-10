<?php

use App\Enums\Role;
use App\Models\Evaluation;
use App\Models\User;
use App\Models\UserQuestionnaire;
use Illuminate\Support\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\EvaluationRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\getJson;
use function Pest\Laravel\json;

beforeEach(function () {
    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    EvaluationRepository::createEvaluations();

    $this->route = route('api.v1.administrative.evaluations.index');
});

it('return 401 response non-login users ', function () {
    $user = UserRepository::getRandomUser();

    $response = getJson(route('api.v1.administrative.evaluations.index'));
    $response->assertUnauthorized();
})->group('administrative/evaluations/index');

it('return 404 response regular login users', function () {
    $user = UserRepository::getRandomUser();
    Sanctum::actingAs($user);

    $response = getJson(route('api.v1.administrative.evaluations.index'));
    $response->assertNotFound();
})->group('administrative/evaluations/index');

test('super admin can obtain evaluation records', function () {
    $user = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($user);

    $response = getJson(route('api.v1.administrative.evaluations.index'));
    $response->assertOk();
})->group('administrative/evaluations/index');

test('admin can obtain evaluation records', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $response = getJson(route('api.v1.administrative.evaluations.index'));
    $response->assertOk();
})->group('administrative/evaluations/index');

test('can paginate evaluation records', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $query = http_build_query([
        'page' => ['size' => 1],
    ]);
    $response = json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', 1)
        ->hasAll(['links', 'meta', 'meta.current_page'])
        ->missing('data.0.attributes.password')
        ->etc());

    $response->assertJsonPath('meta.per_page', 1);
})->group('administrative/evaluations/index');

test('can filter evaluations by user id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $user = User::has('evaluations')->first();

    $query = http_build_query([
        'filter[user]' => $user?->hash_id,
    ]);

    $response = json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    expect(count($results))->toBeGreaterThanOrEqual(1);

    collect($results)->pluck('attributes.user_id')->each(function (string $userId) {
        expect($userId)->toBe($userId);
    });
})->group('administrative/evaluations/index');

test('can filter evaluations by questionnaire id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $evaluation = Evaluation::query()->inRandomOrder()->first();

    $userQuestionnaire = UserQuestionnaire::query()->whereId($evaluation?->user_questionnaire_id)->first();
    $questionnaireId = Hashids::encode($userQuestionnaire?->questionnaire_id);

    $query = http_build_query([
        'filter[questionnaire]' => $questionnaireId,
    ]);

    $response = json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    expect(count($results))->toBeGreaterThanOrEqual(1);

    collect($results)->pluck('attributes.questionnaire_id')
        ->each(function (string $id) use ($questionnaireId) {
            expect($id)->toBe($questionnaireId);
        });
})->group('administrative/evaluations/index');

test('can filter evaluations by both questionnaire id and user id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $evaluation = Evaluation::query()->inRandomOrder()->first();

    $userQuestionnaire = UserQuestionnaire::query()->whereId($evaluation?->user_questionnaire_id)->first();
    $userId = Hashids::encode($userQuestionnaire?->user_id);
    $questionnaireId = Hashids::encode($userQuestionnaire?->questionnaire_id);

    $query = http_build_query([
        'filter[user_questionnaire]' => $userId.','.$questionnaireId,
    ]);

    $response = json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    collect($results)->pluck('attributes')
        ->each(function (array $evaluation) use ($userId, $questionnaireId) {
            expect($userId)->toBe($userId);
            expect($questionnaireId)->toBe($questionnaireId);
        });
})->group('administrative/evaluations/index');

test('can filter evaluations by marks percentage', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $lowerMarksPercentage = 10;
    $highestMarksPercentage = 50;

    $query = http_build_query([
        'filter[marks_percentage]' => $lowerMarksPercentage.','.$highestMarksPercentage,
    ]);

    $response = json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    collect($results)->pluck('attributes.marks_percentage')
        ->each(function (float $marksPercentage) use ($lowerMarksPercentage, $highestMarksPercentage) {
            expect($marksPercentage)
                ->toBeGreaterThanOrEqual($lowerMarksPercentage)
                ->toBeLessThanOrEqual($highestMarksPercentage);
        });
})->group('administrative/evaluations/index');

test('can filter evaluations by user_questionnaire_id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $evaluation = Evaluation::query()->inRandomOrder()->first();

    $hashedUqId = \Vinkla\Hashids\Facades\Hashids::encode($evaluation->user_questionnaire_id);

    $query = http_build_query([
        'filter[uq_id]' => $hashedUqId,
    ]);

    $response = json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    collect($results)->pluck('attributes.user_questionnaire_id')
        ->each(function (string $id) use ($hashedUqId) {
            expect($id)->toBe($hashedUqId);
        });
})->group('administrative/evaluations/index');

test('can sort evaluations by time taken', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $query = http_build_query([
        'sort' => '-time_taken',
    ]);

    $response = json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    $data = collect($results)->pluck('attributes.time_taken');

    $sortedData = $data->sortDesc()->values();

    expect($data->all())->toBe($sortedData->all());
})->group('administrative/evaluations/index');

test('can sort evaluations by correct answers', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $query = http_build_query([
        'sort' => '-no_of_correct_answers',
    ]);

    $response = json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    $data = collect($results)->pluck('attributes.no_of_correct_answers');

    $sortedData = $data->sortDesc()->values();

    expect($data->all())->toBe($sortedData->all());
})->group('administrative/evaluations/index');

test('can sort evaluations by no of answers questions', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $query = http_build_query([
        'sort' => 'no_of_answered_questions',
    ]);

    $response = json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    $data = collect($results)->pluck('attributes.no_of_answered_questions');

    $sortedData = $data->sort()->values();

    expect($data->all())->toBe($sortedData->all());
})->group('administrative/evaluations/index');

test('can sort evaluations by marks percentage', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $query = http_build_query([
        'sort' => '-marks_percentage',
    ]);

    $response = json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    $data = collect($results)->pluck('attributes.marks_percentage');

    $sortedData = $data->sortDesc()->values();

    expect($data->all())->toBe($sortedData->all());
})->group('administrative/evaluations/index');

test('can sort evaluations by total points earned', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $query = http_build_query([
        'sort' => '-total_points_earned',
    ]);

    $response = json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    $data = collect($results)->pluck('attributes.total_points_earned');

    $sortedData = $data->sortDesc()->values();

    expect($data->all())->toBe($sortedData->all());
})->group('administrative/evaluations/index');

test('can sort evaluations by total points allocated', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $query = http_build_query([
        'sort' => '-total_points_allocated',
    ]);

    $response = json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    $data = collect($results)->pluck('attributes.total_points_allocated');

    $sortedData = $data->sortDesc()->values();

    expect($data->all())->toBe($sortedData->all());
})->group('administrative/evaluations/index');

test('can sort evaluations by created at', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $query = http_build_query([
        'sort' => '-created_at',
    ]);

    $response = json('GET', $this->route.'?'.$query);
    $response->assertOk();

    $results = $response->decodeResponseJson()['data'];

    $data = collect($results)->pluck('attributes.created_at')->map(function ($created_at) {
        return Carbon::parse($created_at)->getTimestamp();
    });

    $sortedData = $data->sortDesc()->values();

    expect($data->all())->toBe($sortedData->all());
})->group('administrative/evaluations/index');
