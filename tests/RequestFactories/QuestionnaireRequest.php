<?php

namespace Tests\RequestFactories;

use App\Models\Category;
use App\Models\Questionnaire;
use Tests\Repositories\CategoryRepository;
use Worksome\RequestFactories\RequestFactory;

class QuestionnaireRequest extends RequestFactory
{
    public function definition(): array
    {
        $data = Questionnaire::factory()->make()->toArray();

        $categoryIds = CategoryRepository::getRandomCategories(2)
            ->transform(fn (Category $category) => $category->hash_id)
            ->all();

        $data['categories'] = $categoryIds;

        return $data;
    }
}
