<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\QuestionRepository;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\QuestionRequest;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->route = route('api.v1.administrative.questions.store');
});

it('return 401 unauthorized response for non-login users', function () {
    $response = postJson($this->route);
    $response->assertUnauthorized();
})->group('api/v1/administrative/question/create');

it('allows administrative users to create question', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $questionnairesCount = QuestionRepository::getTotalQuestionsCount();

    $response = postJson($this->route);
    $response->assertCreated();

    assertDatabaseCount('questions', $questionnairesCount + 1);

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type',
        'data.attributes')
        ->etc()
    );
})->fakeRequest(QuestionRequest::class)
    ->group('api/v1/administrative/question/create');

it('can attach categories to question', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $payload = QuestionRequest::new()->create();
    $categories = $payload['categories'];

    $questionnairesCount = QuestionRepository::getTotalQuestionsCount();

    $response = postJson($this->route, $payload);
    $response->assertCreated();

    assertDatabaseCount('questions', $questionnairesCount + 1);

    $lastInsertedQuestion = QuestionRepository::getLastInsertedRecord();
    $lastInsertedQuestionCategoryHashIds = $lastInsertedQuestion->categories()->pluck('categories.id')->all();

    $categoryModelIds = \App\Helpers::getModelIdsFromHashIds($categories);

    expect($lastInsertedQuestionCategoryHashIds)->toBe($categoryModelIds);

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type',
        'data.attributes')
        ->etc()
    );
})->group('api/v1/administrative/question/create');

/*it('allows administrative users to upload images with question', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $questionsCount = QuestionRepository::getTotalQuestionsCount();

    QuestionRequest::new()->withOneImage('image.jpg')->fake();

    \Illuminate\Support\Facades\Storage::fake(config('media-library.disk_name'));

    $response = postJson($this->route);
    $response->assertCreated();

    assertDatabaseCount('questions', $questionsCount + 1);

    $uploadedMedia = QuestionRepository::getLastInsertedRecord()->getMedia()[0];

    \Illuminate\Support\Facades\Storage::disk(config('media-library.disk_name'))
        ->assertExists("{$uploadedMedia->id}/{$uploadedMedia->file_name}");

    $response->assertJson(fn(AssertableJson $json) => $json->hasAll('data.id', 'data.type',
        'data.attributes')
        ->etc()
    );
})->group('api/v1/administrative/question/create');*/
