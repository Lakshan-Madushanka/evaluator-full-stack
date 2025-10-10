<?php

use App\Models\UserQuestionnaire;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\getJson;

it('it can throttle request', function () {
    // Sending 5 requests
    for ($i = 1; $i <= 5; $i++) {
        getJson(route('api.v1.users.questionnaires.checkAvailable',
            ['code' => \Illuminate\Support\Str::uuid()]));
    }

    // 6th request should be throttled
    $response = getJson(route('api.v1.users.questionnaires.checkAvailable',
        ['code' => \Illuminate\Support\Str::uuid()]));

    $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_TOO_MANY_REQUESTS);
})->group('regular/users/questionnaires/checkAvailable');

it('return available false if questionnaire is not available for a user', function () {
    $questionnaire = UserQuestionnaire::query()
        ->where('attempts', 0)
        ->firstOrFail();
    $questionnaire->expires_at = now()->subMinutes(30);
    $questionnaire?->save();
    $questionnaire->refresh();

    $response = getJson(route('api.v1.users.questionnaires.checkAvailable',
        ['code' => $questionnaire?->code]));

    $response->assertOk();
    $response->assertJsonPath('available', false);
})->group('regular/users/questionnaires/checkAvailable');

it('can check if questionnaire is available for a user', function () {
    $questionnaire = UserQuestionnaire::query()
        ->where('attempts', 0)
        ->firstOrFail();
    $questionnaire->expires_at = now()->addMinutes(30);
    $questionnaire?->save();
    $questionnaire->refresh();

    $response = getJson(route('api.v1.users.questionnaires.checkAvailable',
        ['code' => $questionnaire?->code]));

    $response->assertOk();
    $response->assertJson(fn (AssertableJson $json) => $json->hasAll([
        'name',
        'single_answer_type',
        'no_of_questions',
        'allocated_time',
        'expires_at',
    ])->etc()
    );
})->group('regular/users/questionnaires/checkAvailable');
