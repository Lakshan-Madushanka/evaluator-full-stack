<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->route = route('api.v1.administrative.questionnaires.store');
});

it('return 401 unauthorized response for non-login users', function () {
    $response = postJson($this->route);
    $response->assertUnauthorized();
})->group('api/v1/administrative/questionnaire/create');

it('allows administrative users to store questionnaire', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $questionnairesCount = \Tests\Repositories\QuestionnaireRepository::getTotalQuestionnairesCount();

    $response = postJson($this->route);
    $response->assertCreated();

    assertDatabaseCount('questionnaires', $questionnairesCount + 1);

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type',
        'data.attributes')
        ->etc()
    );
})->fakeRequest(\Tests\RequestFactories\QuestionnaireRequest::class)
    ->group('api/v1/administrative/questionnaire/create');

it('can attach categories to questionnaire', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $payload = \Tests\RequestFactories\QuestionnaireRequest::new()->create();
    $categories = $payload['categories'];

    $questionnairesCount = \Tests\Repositories\QuestionnaireRepository::getTotalQuestionnairesCount();

    $response = postJson($this->route, $payload);
    $response->assertCreated();

    assertDatabaseCount('questionnaires', $questionnairesCount + 1);

    $lastInsertedQuestionnaire = \Tests\Repositories\QuestionnaireRepository::getLastInsertedRecord();
    $lastInsertedQuestionnaireCategoryHashIds = $lastInsertedQuestionnaire->categories()->pluck('categories.id')->all();

    $categoryModelIds = \App\Helpers::getModelIdsFromHashIds($categories);

    expect($lastInsertedQuestionnaireCategoryHashIds)->toBe($categoryModelIds);

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type',
        'data.attributes')
        ->etc()
    );
})->group('api/v1/administrative/questionnaire/create');
