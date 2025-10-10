<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\QuestionnaireRepository;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\QuestionnaireRequest;

use function Pest\Laravel\putJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = putJson(route('api.v1.administrative.questionnaires.update', ['questionnaire' => 1]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/questionnaire/update');

it('allows administrative users to update questionnaire', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $questionnaire = QuestionnaireRepository::getRandomQuestionnaire();

    $difficulty = collect(\App\Enums\Difficulty::cases())
        ->filter(function (App\Enums\Difficulty $difficulty) use ($questionnaire) {
            return $difficulty->value !== $questionnaire->difficulty->value;
        })->first();

    QuestionnaireRequest::new(['difficulty' => $difficulty->value])->fake();

    $response = putJson(route('api.v1.administrative.questionnaires.update', ['questionnaire' => $questionnaire->hash_id]));

    $response->assertOk();

    $questionnaire->refresh();

    expect($questionnaire->difficulty->value)->toBe($difficulty->value);

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type', 'data.attributes')
        ->where('data.attributes.difficulty', $difficulty->name)
        ->etc()
    );
})->fakeRequest(QuestionnaireRequest::class)
    ->group('api/v1/administrative/questionnaire/update');

it('can sync categories to questionnaire', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $questionnaire = QuestionnaireRepository::getRandomQuestionnaire();

    $questionnaireCategoryIds = $questionnaire->categories()->pluck('categories.id')->all();

    $newCategoryIds = \App\Models\Category::query()
        ->whereNotIn('id', $questionnaireCategoryIds)
        ->limit(2)
        ->pluck('id')
        ->all();

    $categoriesHashIds = \App\Helpers::getHashIdsFromModelIds($newCategoryIds);

    QuestionnaireRequest::new(['categories' => $categoriesHashIds])->fake();

    $response = putJson(route('api.v1.administrative.questionnaires.update', ['questionnaire' => $questionnaire->hash_id]));
    $response->assertOk();

    $questionnaire->refresh();

    $updatedQuestionnaireCategoryIds = $questionnaire->categories()->pluck('categories.id')->all();

    expect($updatedQuestionnaireCategoryIds)->toBe($newCategoryIds);

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type',
        'data.attributes')
        ->etc()
    );
})->group('api/v1/administrative/questionnaire/update');
