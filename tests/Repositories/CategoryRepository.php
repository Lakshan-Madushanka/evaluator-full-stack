<?php

namespace Tests\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    public static function getRandomCategory(): Category
    {
        return Category::inRandomOrder()->first();
    }

    public static function getTotalCategoriesCount(): int
    {
        return Category::count();
    }

    public static function getRandomCategories(int $limit = 10): Collection
    {
        return Category::inRandomOrder()->limit($limit)->get();
    }
}
