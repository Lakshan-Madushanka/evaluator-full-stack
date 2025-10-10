<?php

use App\Enums\Role;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\CategoryRepository;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\CategoryRequest;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\deleteJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = deleteJson(route('api.v1.administrative.categories.delete', ['category' => 1]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/category/delete');

it('return 404 not found response for admin users', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $response = deleteJson(route('api.v1.administrative.categories.delete', ['category' => 1]));
    $response->assertNotFound();
})->group('api/v1/administrative/category/delete');

it('allows super admin users to delete category', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::SUPER_ADMIN));

    $category = CategoryRepository::getRandomCategory();
    $categoriesCount = CategoryRepository::getTotalCategoriesCount();

    $response = deleteJson(route('api.v1.administrative.categories.delete', ['category' => $category->hash_id]));
    $response->assertNoContent();

    assertDatabaseCount('categories', $categoriesCount - 1);
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/category/delete');
