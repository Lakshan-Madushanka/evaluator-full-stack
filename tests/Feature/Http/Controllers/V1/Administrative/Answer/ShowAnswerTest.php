<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\CategoryRequest;

use function Pest\Laravel\getJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = getJson(route('api.v1.administrative.answers.show', ['answer' => 1]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/answer/show');

it('allows administrative users to retrieve a answer by hash id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $answerHashId = \Tests\Repositories\AnswerRepository::getRandomAnswer()->hash_id;

    $response = getJson(route('api.v1.administrative.answers.show', ['answer' => $answerHashId]));
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type', 'data.attributes')
        ->hasAll(
            'data.attributes.images_count',
            'data.attributes.text',
            'data.attributes.pretty_id',
            'data.attributes.created_at'
        )
        ->where('data.id', $answerHashId)
        ->etc()
    );
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/answer/show');
