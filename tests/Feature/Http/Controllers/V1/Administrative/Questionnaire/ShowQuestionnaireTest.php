<?php

use App\Enums\Role;
use App\Models\Questionnaire;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\CategoryRequest;

use function Pest\Laravel\getJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = getJson(route('api.v1.administrative.questionnaires.show', ['questionnaire' => 1]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/questionnaire/show');

it('allows administrative users to retrieve a question by hash id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $questionHashId = \Tests\Repositories\QuestionnaireRepository::getRandomQuestionnaire()->hash_id;

    $response = getJson(route('api.v1.administrative.questionnaires.show', ['questionnaire' => $questionHashId]));
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type', 'data.attributes')
        ->hasAll(
            [
                'data.attributes.name',
                'data.attributes.difficulty',
                'data.attributes.no_of_easy_questions',
                'data.attributes.no_of_medium_questions',
                'data.attributes.no_of_hard_questions',
                'data.attributes.no_of_questions',
                'data.attributes.no_of_assigned_questions',
                'data.attributes.allocated_time',
            ])
        ->where('data.id', $questionHashId)
        ->etc()
    );
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/questionnaire/show');

it('allows administrative users to include categories with a question by hash id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $questionHashId = Questionnaire::whereHas('categories')->first()->hash_id;

    $query = '?'.http_build_query([
        'include' => 'categories',
    ]);

    $route = route('api.v1.administrative.questionnaires.show', ['questionnaire' => $questionHashId]).$query;

    $response = getJson($route);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type', 'data.attributes')
        ->hasAll('included')
        ->where('included.0.type', 'categories')
        ->etc()
    );
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/questionnaire/show');
