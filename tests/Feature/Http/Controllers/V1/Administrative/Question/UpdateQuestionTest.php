<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\QuestionRepository;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\QuestionRequest;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\putJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = putJson(route('api.v1.administrative.questions.update', ['question' => 1]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/question/update');

it('allows administrative users to update question', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $question = QuestionRepository::getRandomQuestion();

    $difficulty = collect(\App\Enums\Difficulty::cases())
        ->filter(function (App\Enums\Difficulty $difficulty) use ($question) {
            return $difficulty->value !== $question->difficulty->value;
        })->first();

    QuestionRequest::new(['difficulty' => $difficulty->value])->fake();

    $response = putJson(route('api.v1.administrative.questions.update', ['question' => $question->hash_id]));

    $response->assertOk();

    $question->refresh();

    expect($question->difficulty->value)->toBe($difficulty->value);

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type', 'data.attributes')
        ->where('data.attributes.hardness', $difficulty->name)
        ->etc()
    );
})->fakeRequest(QuestionRequest::class)
    ->group('api/v1/administrative/question/update');

it('can sync categories to question', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $question = QuestionRepository::getRandomQuestion();

    $questionCategoryIds = $question->categories()->pluck('categories.id')->all();

    $newCategoryIds = \App\Models\Category::query()
        ->whereNotIn('id', $questionCategoryIds)
        ->limit(2)
        ->pluck('id')
        ->all();

    $categoriesHashIds = \App\Helpers::getHashIdsFromModelIds($newCategoryIds);

    QuestionRequest::new(['categories' => $categoriesHashIds])->fake();

    $response = putJson(route('api.v1.administrative.questions.update', ['question' => $question->hash_id]));
    $response->assertOk();

    $question->refresh();

    $updatedQuestionCategoryIds = $question->categories()->pluck('categories.id')->all();

    expect($updatedQuestionCategoryIds)->toBe($newCategoryIds);

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type',
        'data.attributes')
        ->etc()
    );
})->group('api/v1/administrative/question/update');

// it('allows administrative users to remove a media attached to question', function () {
//    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));
//
//    /*
//     *Uploading image
//     */
//    $questionsCount = QuestionRepository::getTotalQuestionsCount();
//
//    QuestionRequest::new()->withOneImage('image.jpg')->fake();
//
//    $response = postJson(route('api.v1.administrative.questions.store'));
//    $response->assertCreated();
//
//    assertDatabaseCount('questions', $questionsCount + 1);
//
//    $lastUploadedRecord = QuestionRepository::getLastInsertedRecord();
//
//    $uploadedMedia = $lastUploadedRecord->getMedia()[0];
//
//    \Illuminate\Support\Facades\Storage::disk(config('media-library.disk_name'))
//        ->assertExists("{$uploadedMedia->id}/{$uploadedMedia->file_name}");
//
//    $response->assertJson(fn(AssertableJson $json) =>
//    $json->hasAll('data.id', 'data.type', 'data.attributes')
//        ->etc()
//    );
//
//    /*
//     *Deleting image
//     */
//    QuestionRequest::new()->withDeletableImageId($uploadedMedia->id)->fake();
//
//    $response2 = putJson(
//        route('api.v1.administrative.questions.update', ['question' => $lastUploadedRecord->hash_id]),
//    );
//    $response2->assertOk();
//
//    \Illuminate\Support\Facades\Storage::disk(config('media-library.disk_name'))
//        ->assertMissing("{$uploadedMedia->id}/{$uploadedMedia->file_name}");
//
// })->group('api/v1/administrative/question/update');
