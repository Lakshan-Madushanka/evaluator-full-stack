<?php

namespace Tests\Repositories;

use App\Models\Answer;
use Illuminate\Database\Eloquent\Collection;

class AnswerRepository
{
    public static function getRandomAnswer(): Answer
    {
        return Answer::inRandomOrder()->first();
    }

    public static function getTotalAnswersCount(): int
    {
        return Answer::count();
    }

    public static function getLastInsertedRecord(): Answer
    {
        return Answer::query()
            ->orderByDesc('id')
            ->first();
    }

    public static function getRandomAnswers(int $limit = 10): Collection
    {
        return Answer::query()
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
