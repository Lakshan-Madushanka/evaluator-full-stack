<?php

namespace Tests\Repositories;

use App\Enums\Difficulty;
use App\Models\Question;
use App\Models\Questionnaire;
use Illuminate\Database\Eloquent\Collection;
use Vinkla\Hashids\Facades\Hashids;

class QuestionRepository
{
    public static function getRandomQuestion(): Question
    {
        return Question::inRandomOrder()->first();
    }

    public static function getTotalQuestionsCount(): int
    {
        return Question::count();
    }

    public static function getLastInsertedRecord(): Question
    {
        return Question::query()
            ->orderByDesc('id')
            ->first();
    }

    public static function getRandomQuestions(): Collection
    {
        return Question::query()
            ->inRandomOrder()
            ->limit(1)
            ->get();
    }

    public static function pluckCompletedQuestionsHashIds(
        int $limit,
        Questionnaire $questionnaire
    ): \Illuminate\Support\Collection {
        $questionnaireCategoryIds = $questionnaire->categories()->pluck('categories.id');

        return Question::query()
            ->eligible($questionnaire)
            ->limit($limit)
            ->pluck('id')
            ->transform(fn (int $id) => Hashids::encode($id));
    }

    public static function pluckCompletedQuestionsHashIdsByDifficulty(
        int $limit,
        Difficulty $difficulty,
        Questionnaire $questionnaire
    ): \Illuminate\Support\Collection {
        $questionnaireCategoryIds = $questionnaire->categories()->pluck('categories.id');

        return Question::query()
            ->where('difficulty', $difficulty)
            ->eligible($questionnaire)
            ->limit($limit)
            ->pluck('id')
            ->transform(fn (int $id) => Hashids::encode($id));
    }
}
