<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\QuestionRepository;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\QuestionRequest;

use function Pest\Laravel\postJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = \Pest\Laravel\postJson(
        route('api.v1.uploads.massDelete',
            [
                'type' => 'questions',
                'modelId' => 'wserkl',
                'imageId' => -1000,
            ]
        ),
    );
    $response->assertUnauthorized();
})->group('api/v1/administrative/question/uploads');

it('allows administrative users to delete images that attached to a question', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    /*
     *Uploading image
     */
    $question = QuestionRepository::getRandomQuestion();

    QuestionRequest::new()->withOneImage('image.jpg')->fake();

    \Illuminate\Support\Facades\Storage::fake(config('media-library.disk_name'));

    $response = postJson(route('api.v1.uploads.store', ['type' => 'questions', 'id' => $question->hash_id]));

    $response->assertCreated();

    $uploadedMedia = $question->getMedia()[0];

    \Illuminate\Support\Facades\Storage::disk(config('media-library.disk_name'))
        ->assertExists("{$uploadedMedia->id}/{$uploadedMedia->file_name}");

    /*
     *Deleting image
     */
    QuestionRequest::new()->withDeletableImageId($uploadedMedia->id)->fake();

    $response2 = \Pest\Laravel\postJson(
        route('api.v1.uploads.massDelete',
            [
                'type' => 'questions',
            ]
        ),
        ['ids' => [$uploadedMedia->uuid]]
    );
    $response2->assertNoContent();

    \Illuminate\Support\Facades\Storage::disk(config('media-library.disk_name'))
        ->assertMissing("{$uploadedMedia->id}/{$uploadedMedia->file_name}");
})->group('api/v1/administrative/question/uploads');
