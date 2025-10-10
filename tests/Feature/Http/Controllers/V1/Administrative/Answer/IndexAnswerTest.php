<?php

use App\Enums\Difficulty;
use App\Enums\Role;
use App\Models\Answer;
use Carbon\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\AnswerRepository;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\AnswerRequest;

use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->route = route('api.v1.administrative.answers.index');
});

it('return 401 unauthorized response for non-login users', function () {
    $response = getJson($this->route);
    $response->assertUnauthorized();
})->group('api/v1/administrative/answer/index');

it('allows administrative users to retrieve all answers', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $answersCount = AnswerRepository::getTotalAnswersCount();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $query = '?'.http_build_query([
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', $answersCount)->etc());
})->group('api/v1/administrative/answer/index');

it('can sorts all answers by created at column', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $answersCount = AnswerRepository::getTotalAnswersCount();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $query = '?'.http_build_query([
        'sort' => '-created_at',
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', $answersCount)->etc());

    $data = $response->decodeResponseJson()['data'];
    $data = collect($data)->pluck('attributes.created_at')->map(function ($created_at) {
        return Carbon::parse($created_at)->getTimestamp();
    });

    $sortedData = $data->sortDesc()->values();

    expect($data->all())->toBe($sortedData->all());
})->group('api/v1/administrative/answer/index');

it('can filter all answers by its content', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $text = \Illuminate\Support\Str::random().'test'.\Illuminate\Support\Str::random();

    Answer::create(AnswerRequest::new(['text' => $text])->create());

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $filteredDifficulty = Difficulty::EASY->name;

    $query = '?'.http_build_query([
        'filter' => ['text' => 'test'],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->pluck('attributes.text')
        ->each(fn (string $content) => expect(str_contains($content, 'test'))->toBeTrue());
})->group('api/v1/administrative/answer/index');

it('sort records by id desc by default', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $answersCount = AnswerRepository::getTotalAnswersCount();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $query = '?'.http_build_query([
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', $answersCount)->etc());

    $data = $response->decodeResponseJson()['data'];
    $data = collect($data)->pluck('attributes.id');

    $sortedData = $data->sortDesc()->values();

    expect($data->all())->toBe($sortedData->all());
})->group('api/v1/administrative/answer/index');

it('can filter all questions by its pretty id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $answer = AnswerRepository::getRandomAnswer();

    $query = '?'.http_build_query([
        'filter' => ['pretty_id' => $answer->pretty_id],
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    $prettyIds = collect($data)
        ->pluck('attributes.pretty_id');

    $prettyIds->each(function (string $prettyId) use ($answer) {
        expect($prettyId)->toBe($answer->pretty_id);
    });
})->group('api/v1/administrative/answer/index');
