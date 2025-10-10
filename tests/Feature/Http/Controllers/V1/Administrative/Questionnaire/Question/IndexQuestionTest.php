<?php

use App\Enums\Role;
use App\Models\Questionnaire;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\CategoryRequest;

use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->questionnaire = Questionnaire::whereHas('questions')->first();

    $this->route = route(
        'api.v1.administrative.questionnaires.questions.index',
        ['questionnaire' => $this->questionnaire->hash_id]
    );
});

it('return 401 unauthorized response for non-login users', function () {
    $response = getJson($this->route);
    $response->assertUnauthorized();
})->group('api/v1/administrative/questionnaire/question/index');

it('allows administrative users to retrieve all questions of a questionnaires', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $questionsCount = $this->questionnaire->questions->count();

    config(['json-api-paginate.max_results' => PHP_INT_MAX]);

    $query = '?'.http_build_query([
        'page' => ['size' => PHP_INT_MAX],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', $questionsCount)
        ->has('data.0', fn (AssertableJson $json) => $json->where('type', 'questions')
            ->etc()
        )
        ->etc()
    );
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/questionnaire/question/index');
