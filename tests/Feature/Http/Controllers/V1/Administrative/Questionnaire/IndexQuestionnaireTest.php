<?php

use App\Enums\Difficulty;
use App\Enums\Role;
use App\Models\Category;
use App\Models\Questionnaire;
use Carbon\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\QuestionnaireRepository;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\CategoryRequest;
use Tests\RequestFactories\QuestionnaireRequest;

use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->route = route('api.v1.administrative.questionnaires.index');
});

it('return 401 unauthorized response for non-login users', function () {
    $response = getJson($this->route);
    $response->assertUnauthorized();
})->group('api/v1/administrative/questionnaire/index');

it('allows administrative users to retrieve all questionnaires', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $questionnairesCount = QuestionnaireRepository::getTotalQuestionnairesCount();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $query = '?'.http_build_query([
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', $questionnairesCount)->etc());
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/questionnaire/index');

it('can sorts all questionnaires by created at column', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $questionnairesCount = QuestionnaireRepository::getTotalQuestionnairesCount();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $query = '?'.http_build_query([
        'sort' => '-created_at',
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', $questionnairesCount)->etc());

    $data = $response->decodeResponseJson()['data'];
    $data = collect($data)->pluck('attributes.created_at')->map(function ($created_at) {
        return Carbon::parse($created_at)->getTimestamp();
    });

    $sortedData = $data->sortDesc()->values();

    expect($data->all())->toBe($sortedData->all());
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/questionnaire/index');

it('can filter all questionnaires by categories name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $questionName = Category::whereHas('questionnaires')->first()->name;

    $query = '?'.http_build_query([
        'filter' => ['categories.name' => $questionName],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    $categoriesNames = collect($data)
        ->pluck('attributes.categories')
        ->collapse()
        ->pluck('attributes.name')
        ->filter();

    $categoriesNames->each(function (string $name) use ($questionName) {
        expect($name)->toBe($questionName);
    });
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/questionnaire/index');

it('can filter all questionnaires by difficulty', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $filteredDifficulty = Difficulty::EASY->name;

    $query = '?'.http_build_query([
        'filter' => ['difficulty' => $filteredDifficulty],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->pluck('attributes.difficulty')
        ->filter()
        ->each(fn (string $difficulty) => expect($filteredDifficulty)->toBe($difficulty));
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/questionnaire/index');

it('can filter all questionnaires by its name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $text = 'z'.\Illuminate\Support\Str::random();

    $data = QuestionnaireRequest::new(['name' => $text])->getFactoryData()->getRequestedData();

    Questionnaire::create(Arr::except($data, 'categories'));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $filteredDifficulty = Difficulty::EASY->name;

    $query = '?'.http_build_query([
        'filter' => ['name' => 'z'],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->pluck('attributes.name')
        ->each(function (string $content) {
            $exists = str_contains($content, 'z') || str_contains($content, 'Z');
            expect($exists)->toBeTrue();
        });
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/questionnaire/index');

it('can filter all questionnaires by its questions range', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $text = \Illuminate\Support\Str::random().'test'.\Illuminate\Support\Str::random();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $min = 10;
    $max = 100;

    $query = '?'.http_build_query([
        'filter' => ['no_of_questions' => ['min' => $min, 'max' => $max]],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->each(function (array $data) use ($min, $max) {
        $noOfQuestions = $data['attributes']['no_of_questions'];

        expect($noOfQuestions >= $min && $noOfQuestions <= $max)->toBeTrue();
    });
})->group('api/v1/administrative/questionnaire/index');

it('can filter all questionnaires by its hard questions range', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $text = \Illuminate\Support\Str::random().'test'.\Illuminate\Support\Str::random();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $min = 10;
    $max = 100;

    $query = '?'.http_build_query([
        'filter' => ['no_of_hard_questions' => ['min' => $min, 'max' => $max]],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->each(function (array $data) use ($min, $max) {
        $noOfQuestions = $data['attributes']['no_of_hard_questions'];

        expect($noOfQuestions >= $min && $noOfQuestions <= $max)->toBeTrue();
    });
})->group('api/v1/administrative/questionnaire/index');

it('can filter all questionnaires by its easy questions range', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $text = \Illuminate\Support\Str::random().'test'.\Illuminate\Support\Str::random();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $min = 10;
    $max = 100;

    $query = '?'.http_build_query([
        'filter' => ['no_of_easy_questions' => ['min' => $min, 'max' => $max]],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->each(function (array $data) use ($min, $max) {
        $noOfQuestions = $data['attributes']['no_of_easy_questions'];

        expect($noOfQuestions >= $min && $noOfQuestions <= $max)->toBeTrue();
    });
})->group('api/v1/administrative/questionnaire/index');

it('can filter all questionnaires by its medium questions range', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $text = \Illuminate\Support\Str::random().'test'.\Illuminate\Support\Str::random();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $min = 10;
    $max = 100;

    $query = '?'.http_build_query([
        'filter' => ['no_of_medium_questions' => ['min' => $min, 'max' => $max]],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->each(function (array $data) use ($min, $max) {
        $noOfQuestions = $data['attributes']['no_of_medium_questions'];

        expect($noOfQuestions >= $min && $noOfQuestions <= $max)->toBeTrue();
    });
})->group('api/v1/administrative/questionnaire/index');

it('can filter all questionnaires by its allocated time range', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $text = \Illuminate\Support\Str::random().'test'.\Illuminate\Support\Str::random();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $min = 10;
    $max = 100;

    $query = '?'.http_build_query([
        'filter' => ['allocated_time' => ['min' => $min, 'max' => $max]],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->each(function (array $data) use ($min, $max) {
        $allocatedTime = $data['attributes']['allocated_time'];

        expect($allocatedTime >= $min && $allocatedTime <= $max)->toBeTrue();
    });
})->group('api/v1/administrative/questionnaire/index');

it('can filter by single answers type questions', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $query = '?'.http_build_query([
        'filter' => ['single_answers_type' => true],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    $prettyIds = collect($data)
        ->pluck('attributes.single_answers_type');

    $prettyIds->each(function (bool $singleAnswersType) {
        expect($singleAnswersType)->toBeTrue();
    });
})->group('api/v1/administrative/questionnaire/index');

it('can filter all questionnaires by its completeness', function (bool $completed) {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $query = '?'.http_build_query([
        'filter' => ['completed' => $completed],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->pluck('attributes.completed')
        ->each(function (bool $isCcompleted) use ($completed) {
            expect($isCcompleted)->toBe($completed);
        });
})->with([true, false])
    ->group('api/v1/administrative/questionnaire/index');
