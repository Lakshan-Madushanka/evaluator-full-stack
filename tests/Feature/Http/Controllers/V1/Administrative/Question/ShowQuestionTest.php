<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\CategoryRequest;

use function Pest\Laravel\getJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = getJson(route('api.v1.administrative.questions.show', ['question' => 1]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/question/show');

it('allows administrative users to retrieve a question by hash id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $questionHashId = \Tests\Repositories\QuestionRepository::getRandomQuestion()->hash_id;

    $response = getJson(route('api.v1.administrative.questions.show', ['question' => $questionHashId]));
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type', 'data.attributes')
        ->hasAll([
            'data.attributes.images_count',
            'data.attributes.no_of_assigned_answers',
            'data.attributes.pretty_id',
        ])
        ->where('data.id', $questionHashId)
        ->etc()
    );
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/question/show');

it('allows administrative users to include categories with a question by hash id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $questionHashId = \App\Models\Question::whereHas('categories')->first()->hash_id;

    $query = '?'.http_build_query([
        'include' => 'categories',
    ]);

    $route = route('api.v1.administrative.questions.show', ['question' => $questionHashId]).$query;

    $response = getJson($route);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type', 'data.attributes')
        ->hasAll('included')
        ->where('included.0.type', 'categories')
        ->etc()
    );
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/question/show');
