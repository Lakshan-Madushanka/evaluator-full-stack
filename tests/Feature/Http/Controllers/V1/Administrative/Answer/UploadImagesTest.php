<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\QuestionRepository;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\QuestionRequest;

use function Pest\Laravel\postJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = \Pest\Laravel\postJson(route('api.v1.uploads.store', ['type' => 'questions', 'id' => '456']));
    $response->assertUnauthorized();
})->group('api/v1/administrative/question/uploads');

it('allows administrative users to upload image for question', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $question = QuestionRepository::getRandomQuestion();

    QuestionRequest::new()->withOneImage('image.jpg')->fake();

    \Illuminate\Support\Facades\Storage::fake(config('media-library.disk_name'));

    $response = postJson(route('api.v1.uploads.store', ['type' => 'questions', 'id' => $question->hash_id]));

    $response->assertCreated();

    $uploadedMedia = $question->getMedia()[0];

    \Illuminate\Support\Facades\Storage::disk(config('media-library.disk_name'))
        ->assertExists("{$uploadedMedia->id}/{$uploadedMedia->file_name}");
})->group('api/v1/administrative/question/uploads');

it('allows administrative users to upload images for question', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $question = \App\Models\Question::factory()->create();

    QuestionRequest::new()->withImages()->fake();

    \Illuminate\Support\Facades\Storage::fake(config('media-library.disk_name'));

    $response = postJson(route('api.v1.uploads.store', ['type' => 'questions', 'id' => $question->hash_id]));

    $response->assertCreated();

    $uploadedMediaCount = $question->getMedia()->count();

    expect($uploadedMediaCount > 0)->toBeTrue();
})->group('api/v1/administrative/question/uploads');
