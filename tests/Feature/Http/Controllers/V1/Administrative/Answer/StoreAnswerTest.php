<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\AnswerRepository;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\AnswerRequest;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->route = route('api.v1.administrative.answers.store');
});

it('return 401 unauthorized response for non-login users', function () {
    $response = postJson($this->route);
    $response->assertUnauthorized();
})->group('api/v1/administrative/answer/store');

it('allows administrative users to store answer', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $categoriesCount = AnswerRepository::getTotalAnswersCount();

    $response = postJson($this->route);
    $response->assertCreated();

    assertDatabaseCount('answers', $categoriesCount + 1);

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type',
        'data.attributes')
        ->etc()
    );
})->fakeRequest(AnswerRequest::class)
    ->group('api/v1/administrative/answer/store');
