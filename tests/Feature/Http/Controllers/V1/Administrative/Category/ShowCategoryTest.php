<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\CategoryRequest;

use function Pest\Laravel\getJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = getJson(route('api.v1.administrative.categories.show', ['category' => 1]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/category/show');

it('allows administrative users to retrieve all categories by hash id', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $categoryHashId = \Tests\Repositories\CategoryRepository::getRandomCategory()->hash_id;

    $response = getJson(route('api.v1.administrative.categories.show', ['category' => $categoryHashId]));
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type', 'data.attributes')
        ->where('data.id', $categoryHashId)
        ->etc()
    );
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/category/show');
