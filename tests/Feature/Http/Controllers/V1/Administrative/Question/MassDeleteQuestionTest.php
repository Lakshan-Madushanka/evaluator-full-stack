<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->seeder = \Database\Seeders\QuestionSeeder::class;
});

it('return 401 unauthorized response for non-login questions', function () {
    $route = route('api.v1.administrative.questions.mass-delete');
    $response = deleteJson($route);
    $response->assertUnauthorized();
})->group('api/v1/administrative/question/mass-delete');

it('requires ids field to delete records', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $route = route('api.v1.administrative.questions.mass-delete');

    $response = postJson($route);
    $response->assertInvalid(['ids']);
})->group('api/v1/administrative/question/mass-delete');

it('allows administrative questions to mass delete questions', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $questions = \Tests\Repositories\QuestionRepository::getRandomQuestions();
    $hashIds = $questions->pluck('hash_id');

    $route = route('api.v1.administrative.questions.mass-delete');

    $response = postJson($route, ['ids' => $hashIds->all()]);
    $response->assertNoContent();

    $questions->each(function (App\Models\Question $question) {
        assertDatabaseMissing('questions', [
            'id' => $question->id,
        ]);
    });
})->group('api/v1/administrative/question/mass-delete');
