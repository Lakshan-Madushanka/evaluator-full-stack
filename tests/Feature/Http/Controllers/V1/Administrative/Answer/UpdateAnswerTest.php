<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\AnswerRepository;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\AnswerRequest;

use function Pest\Laravel\putJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = putJson(route('api.v1.administrative.answers.update', ['answer' => 1]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/answer/update');

it('allows administrative users to update answer', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $answer = AnswerRepository::getRandomAnswer();

    $text = \Illuminate\Support\Str::random();

    $response = putJson(
        route('api.v1.administrative.answers.update', ['answer' => $answer->hash_id]),
        ['text' => $text]
    );

    $response->assertOk();

    $answer->refresh();

    expect($answer->text)->toBe($text);

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type', 'data.attributes')
        ->where('data.attributes.text', $text)
        ->etc()
    );
})->fakeRequest(AnswerRequest::class)
    ->group('api/v1/administrative/answer/update');
