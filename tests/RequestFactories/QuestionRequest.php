<?php

namespace Tests\RequestFactories;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\UploadedFile;
use Tests\Repositories\CategoryRepository;
use Worksome\RequestFactories\RequestFactory;

class QuestionRequest extends RequestFactory
{
    public function definition(): array
    {
        $data = Question::factory()->make()->toArray();

        $categoryIds = CategoryRepository::getRandomCategories(2)
            ->transform(fn (Category $category) => $category->hash_id)
            ->all();

        $data['categories'] = $categoryIds;

        return $data;
    }

    public function withOneImage(string $name = 'image.jpg'): QuestionRequest
    {
        return $this->state([
            'images' => [UploadedFile::fake()->create($name, 1024)],
        ]);
    }

    public function withImages(): QuestionRequest
    {
        return $this->state([
            'images' => [
                UploadedFile::fake()->create('image.jpg', 1024),
                UploadedFile::fake()->create('image.jpg', 1024),
            ],
        ]);
    }

    public function withOverSizeImage(): QuestionRequest
    {
        return $this->state([
            'images' => [UploadedFile::fake()->create('image.jpg', 10 * 1024)],
        ]);
    }

    public function withDeletableImageId($id): QuestionRequest
    {
        return $this->state([
            'deletable_image_id' => $id,
        ]);
    }
}
