<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\post;

it('throws 404 for invalid route end points', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $question = \Tests\Repositories\QuestionRepository::getRandomQuestion();

    $response = post(route('api.v1.uploads.store', ['type' => 'invalid type', 'id' => $question->hash_id]));

    $response->assertStatus(404);
})->group('api/v1/uploads');
