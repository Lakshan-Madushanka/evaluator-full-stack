<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\EvaluationRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\getJson;

beforeEach(function () {
    EvaluationRepository::createEvaluations();
});

it('return 401 response non-login users ', function () {
    $user = UserRepository::getRandomUser();

    $response = getJson(
        route('api.v1.administrative.evaluations.show', ['evaluation' => EvaluationRepository::getRandomEvaluation()]),
    );
    $response->assertUnauthorized();
})->group('administrative/evaluations/show');

it('return 404 response regular login users', function () {
    $user = UserRepository::getRandomUser();
    Sanctum::actingAs($user);

    $response = getJson(
        route('api.v1.administrative.evaluations.show', ['evaluation' => EvaluationRepository::getRandomEvaluation()]),
    );
    $response->assertNotFound();
})->group('administrative/evaluations/index');

it('allow to get an evaluation records for admin type users', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $evaluation = EvaluationRepository::getRandomEvaluation();

    $response = getJson(
        route('api.v1.administrative.evaluations.show', ['evaluation' => $evaluation->hash_id]),
    );

    $response->assertOk();

})->group('administrative/evaluations/index');
