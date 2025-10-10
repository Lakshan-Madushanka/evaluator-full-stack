<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\CategoryRepository;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\CategoryRequest;

use function Pest\Laravel\putJson;

it('return 401 unauthorized response for non-login users', function () {
    $response = putJson(route('api.v1.administrative.categories.update', ['category' => 1]));
    $response->assertUnauthorized();
})->group('api/v1/administrative/category/update');

it('allows administrative users to update category', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $category = CategoryRepository::getRandomCategory();
    $name = \Illuminate\Support\Str::random();

    $response = putJson(route('api.v1.administrative.categories.update', ['category' => $category->hash_id]),
        ['name' => $name]);
    $response->assertOk();

    $category->refresh();

    expect($category->name)->toBe($name);

    $response->assertJson(fn (AssertableJson $json) => $json->hasAll('data.id', 'data.type',
        'data.attributes')
        ->etc()
    );
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/category/update');
