<?php

use App\Enums\Role;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\UserRepository;
use Tests\RequestFactories\CategoryRequest;

use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->route = route('api.v1.administrative.categories.index');
});

it('return 401 unauthorized response for non-login users', function () {
    $response = getJson($this->route);
    $response->assertUnauthorized();
})->group('api/v1/administrative/category/index');

it('allows administrative users to retrieve all categories', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $categoriesCount = \Tests\Repositories\CategoryRepository::getTotalCategoriesCount();

    $response = getJson($this->route);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', $categoriesCount)->etc());
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/category/index');

it('sorts all categories by name asc order by default', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $categoriesCount = \Tests\Repositories\CategoryRepository::getTotalCategoriesCount();

    $response = getJson($this->route);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', $categoriesCount)->etc());

    $data = $response->decodeResponseJson()['data'];
    $data = collect($data)->pluck('attributes.name');
    $sortedData = $data->sort();

    expect($data->all())->toEqual($sortedData->all());
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/category/index');

it('can sorts all categories by name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $categoriesCount = \Tests\Repositories\CategoryRepository::getTotalCategoriesCount();

    $query = '?'.http_build_query([
        'sort' => '-name',
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) => $json->has('data', $categoriesCount)->etc());

    $data = $response->decodeResponseJson()['data'];
    $data = collect($data)->pluck('attributes.name');
    $sortedData = $data->sortByDesc('name');

    expect($data->all())->toEqual($sortedData->all());
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/category/index');

it('can filter all categories by name', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $name = Str::random();
    $category = \App\Models\Category::create(['name' => $name]);

    $query = '?'.http_build_query([
        'filter' => ['name' => $name],
    ]);

    $response = getJson($this->route.$query);
    $response->assertOk();

    $data = $response->decodeResponseJson()['data'];

    collect($data)->pluck('attributes.name')->each(function (string $catName) use ($name) {
        expect(str_contains($catName, $name))->toBeTrue();
    });
})->fakeRequest(CategoryRequest::class)
    ->group('api/v1/administrative/category/index');
