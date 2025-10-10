<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\CategoryRepository;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\CategoryRequest;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->route = route('api.v1.administrative.categories.store');
});

it('return 401 unauthorized response for non-login users', function () {
    $response = postJson($this->route);
    $response->assertUnauthorized();
})->group('api/v1/administrative/category/create');

it('allows administrative users to create category', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $categoriesCount = CategoryRepository::getTotalCategoriesCount();

    $response = postJson($this->route);
    $response->assertCreated();

    assertDatabaseCount('categories', $categoriesCount + 1);

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type',
        'data.attributes')
        ->etc()
    );
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/category/create');
