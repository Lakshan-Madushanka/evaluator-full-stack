<?php

use App\Enums\Role;
use Database\Seeders\UserSeeder;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->seeder = UserSeeder::class;
});

it('return 401 unauthorized response for non-login answers', function () {
    $route = route('api.v1.administrative.answers.mass-delete');
    $response = deleteJson($route);
    $response->assertUnauthorized();
})->group('api/v1/administrative/answer/mass-delete');

it('requires ids field to delete records', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $route = route('api.v1.administrative.answers.mass-delete');

    $response = postJson($route);
    $response->assertInvalid(['ids']);
})->group('api/v1/administrative/answer/mass-delete');

it('allows administrative answers to mass delete answers', function () {
    $superAdmin = UserRepository::getRandomUser(Role::SUPER_ADMIN);
    Sanctum::actingAs($superAdmin);

    $answers = \Tests\Repositories\AnswerRepository::getRandomAnswers();
    $hashIds = $answers->pluck('hash_id');

    $route = route('api.v1.administrative.answers.mass-delete');

    $response = postJson($route, ['ids' => $hashIds->all()]);
    $response->assertNoContent();

    $answers->each(function (App\Models\Answer $answer) {
        assertDatabaseMissing('answers', [
            'id' => $answer->id,
        ]);
    });
})->group('api/v1/administrative/answer/mass-delete');
