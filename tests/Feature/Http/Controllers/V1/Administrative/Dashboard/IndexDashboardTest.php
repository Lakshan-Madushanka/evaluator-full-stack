<?php

use App\Enums\Role;
use App\Models\Category;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\EvaluationRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->route = route('api.v1.administrative.dashboard');
});

it('return 401 response non-login users ', function () {
    $user = UserRepository::getRandomUser();

    $response = getJson($this->route);
    $response->assertUnauthorized();
})->group('administrative/evaluations/index');

it('return 404 response regular login users', function () {
    $user = UserRepository::getRandomUser();
    Sanctum::actingAs($user);

    $response = getJson($this->route);
    $response->assertNotFound();
})->group('administrative/evaluations/index');

it('return total no of users', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $response = getJson($this->route);

    $superAdminsCount = User::query()
        ->where('role', Role::SUPER_ADMIN)
        ->count();

    $adminsCount = User::query()
        ->where('role', Role::ADMIN)
        ->count();

    $regularUsersCount = User::query()
        ->where('role', Role::REGULAR)
        ->count();

    $response->assertJsonPath('data.attributes.users_count.super_admins', $superAdminsCount);
    $response->assertJsonPath('data.attributes.users_count.admins', $adminsCount);
    $response->assertJsonPath('data.attributes.users_count.regular_users', $regularUsersCount);
});

it('return total no of categories', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $response = getJson($this->route);

    $response->assertJsonPath('data.attributes.no_of_categories', Category::count());
});

it('return total no of questionnaires', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $response = getJson($this->route);

    $response->assertJsonPath('data.attributes.no_of_questionnaires', Questionnaire::count());
});

it('return total no of questions', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $response = getJson($this->route);

    $response->assertJsonPath('data.attributes.no_of_questions', Question::count());
});

it('return categories questionnaires data', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $response = getJson($this->route);

    $countData = Category::query()->inRandomOrder()->withCount('questionnaires')->first();

    $responseData = $response->json('data.attributes.category_questionnaires');

    expect($responseData[$countData?->name])->toEqual($countData?->questionnaires_count);
});

it('return categories questions data', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $response = getJson($this->route);

    $countData = Category::query()
        ->withCount('questions')
        ->get()
        ->mapWithKeys(fn (Category $category) => [$category->name => $category->questions_count]);

    $responseData = $response->json('data.attributes.category_questions');

    expect($responseData)->toMatchArray($countData);
});

it('return latest evaluations data', function () {
    $user = UserRepository::getRandomUser(Role::ADMIN);
    Sanctum::actingAs($user);

    $limit = 10;

    EvaluationRepository::createEvaluations($limit);

    $response = getJson($this->route);

    $evaluations = $response->json('data.attributes.evaluations');

    expect(count($evaluations))->toEqual($limit);
});
