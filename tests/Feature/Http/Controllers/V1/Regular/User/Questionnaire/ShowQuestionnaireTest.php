<?php

use App\Models\UserQuestionnaire;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\getJson;

it('it can throttle request', function () {
    // Sending 5 requests
    for ($i = 1; $i <= 5; $i++) {
        getJson(route('api.v1.users.questionnaires.show',
            ['code' => \Illuminate\Support\Str::uuid()]));
    }

    // 6th request should be throttled
    $response = getJson(route('api.v1.users.questionnaires.show',
        ['code' => \Illuminate\Support\Str::uuid()]));

    $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_TOO_MANY_REQUESTS);
})->group('regular/users/questionnaires/show');

it('can show  questionnaire if available for a user', function () {
    $questionnaire = UserQuestionnaire::query()
        ->where([
            ['attempts', 0],
            ['started_at', null],
        ])
        ->firstOrFail();
    $questionnaire->expires_at = now()->addMinutes(30);
    $questionnaire->save();

    $response = getJson(route('api.v1.users.questionnaires.show',
        ['code' => $questionnaire->code]));

    $questionnaire->refresh();

    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    expect($data)->toBeArray()->not()->toBeEmpty();
    expect($questionnaire->attempts)->toBe(1);
    expect($questionnaire->started_at)->not()->toBeNull();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data')
        ->etc()
    );

    // Resend the request to check started at and attempts column not changed
    $response2 = getJson(route('api.v1.users.questionnaires.show',
        ['code' => $questionnaire?->code]));

    $response2->assertOk();

    $questionnaire2 = $questionnaire->fresh();

    expect($questionnaire2?->attempts)->toBe(1);
    expect($questionnaire->started_at->getTimestamp())->toBe($questionnaire2?->started_at->getTimestamp());
})->group('regular/users/questionnaires/show');
