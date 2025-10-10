<?php

namespace Tests\RequestFactories;

use App\Models\Answer;
use Illuminate\Http\UploadedFile;
use Worksome\RequestFactories\RequestFactory;

class AnswerRequest extends RequestFactory
{
    public function definition(): array
    {
        return Answer::factory()->make()->toArray();
    }

    public function withOneImage(string $name = 'image.jpg'): AnswerRequest
    {
        return $this->state([
            'images' => [UploadedFile::fake()->create($name, 1024)],
        ]);
    }

    public function withImages(): AnswerRequest
    {
        return $this->state([
            'images' => [
                UploadedFile::fake()->create('image.jpg', 1024),
                UploadedFile::fake()->create('image.jpg', 1024),
            ],
        ]);
    }

    public function withOverSizeImage(): AnswerRequest
    {
        return $this->state([
            'images' => [UploadedFile::fake()->create('image.jpg', 10 * 1024)],
        ]);
    }

    public function withDeletableImageId($id): AnswerRequest
    {
        return $this->state([
            'deletable_image_id' => $id,
        ]);
    }
}
