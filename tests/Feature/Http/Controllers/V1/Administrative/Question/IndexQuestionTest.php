<?php

use App\Enums\Difficulty;
use App\Enums\Role;
use App\Models\Category;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\QuestionRepository;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\QuestionRequest;

use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->route = route('api.v1.administrative.questions.index');
});

it('return 401 unauthorized response for non-login users', function () {
    $response = getJson($this->route);
    $response->assertUnauthorized();
})->group('api/v1/administrative/question/index');

it('allows administrative users to retrieve all questions', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $questionsCount = QuestionRepository::getTotalQuestionsCount();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $query = '?'.http_build_query([
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', $questionsCount)->etc());
})->group('api/v1/administrative/question/index');

it('can sorts all questions by created at column', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $questionsCount = QuestionRepository::getTotalQuestionsCount();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $query = '?'.http_build_query([
        'sort' => '-created_at',
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', $questionsCount)->etc());

    $data = $response->decodeResponseJson()['data'];
    $data = collect($data)->pluck('attributes.created_at')->map(function ($created_at) {
        return Carbon::parse($created_at)->getTimestamp();
    });

    $sortedData = $data->sortDesc()->values();

    expect($data->all())->toBe($sortedData->all());
})->group('api/v1/administrative/question/index');

it('can filter all questions by categories name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $questionName = Category::whereHas('questions')->first()->name;

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
})->group('api/v1/administrative/question/index');

it('can filter all questions by difficulty', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $filteredDifficulty = Difficulty::EASY->name;

    $query = '?'.http_build_query([
        'filter' => ['hardness' => $filteredDifficulty],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->pluck('attributes.hardness')
        ->filter()
        ->each(fn (string $difficulty) => expect($filteredDifficulty)->toBe($difficulty));
})->group('api/v1/administrative/question/index');

it('can filter all questions by its content', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $text = \Illuminate\Support\Str::random().'test'.\Illuminate\Support\Str::random();

    $data = QuestionRequest::new(['text' => $text])->getFactoryData()->getRequestedData();

    Question::create(Arr::except($data, 'categories'));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $filteredDifficulty = Difficulty::EASY->name;

    $query = '?'.http_build_query([
        'filter' => ['content' => 'test'],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->pluck('attributes.content')
        ->each(fn (string $content) => expect(str_contains($content, 'test'))->toBeTrue());
})->group('api/v1/administrative/question/index');

it('can filter all questions by its completeness', function (mixed $completeness) {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $text = \Illuminate\Support\Str::random().'test'.\Illuminate\Support\Str::random();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $filteredDifficulty = Difficulty::EASY->name;

    $query = '?'.http_build_query([
        'filter' => ['completed' => $completeness],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->each(function (array $data) {
        $question = Question::findOrFail(\Vinkla\Hashids\Facades\Hashids::decode($data['id'])[0]);

        $question->loadCount('answers');

        expect($question->no_of_answers)->toBe($question->answers_count);
    });
})->with([1, '1', true, 'on', 'yes'])
    ->group('api/v1/administrative/question/index');

it('can filter all questions by its incompleteness', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $text = \Illuminate\Support\Str::random().'test'.\Illuminate\Support\Str::random();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $filteredDifficulty = Difficulty::EASY->name;

    $query = '?'.http_build_query([
        'filter' => ['completed' => false],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->each(function (array $data) {
        $question = Question::findOrFail(\Vinkla\Hashids\Facades\Hashids::decode($data['id'])[0]);

        $question->loadCount('answers');

        expect($question->no_of_answers !== $question->answers_count)->toBeTrue();
    });
})->group('api/v1/administrative/question/index');

it('can filter all questions by its pretty id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $question = QuestionRepository::getRandomQuestion();

    $query = '?'.http_build_query([
        'filter' => ['pretty_id' => $question->pretty_id],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    $prettyIds = collect($data)
        ->pluck('attributes.pretty_id');

    $prettyIds->each(function (string $prettyId) use ($question) {
        expect($prettyId)->toBe($question->pretty_id);
    });
})->group('api/v1/administrative/question/index');

it('can filter by single answers type questions', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $query = '?'.http_build_query([
        'filter' => ['answers_type_single' => true],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    $prettyIds = collect($data)
        ->pluck('attributes.answers_type_single');

    $prettyIds->each(function (bool $singleAnswersType) {
        expect($singleAnswersType)->toBeTrue();
    });
})->group('api/v1/administrative/question/index');
