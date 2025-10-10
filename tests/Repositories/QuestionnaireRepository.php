<?php

namespace Tests\Repositories;

use App\Models\Questionnaire;
use Illuminate\Database\Eloquent\Collection;

class QuestionnaireRepository
{
    public static function getRandomQuestionnaire(): Questionnaire
    {
        return Questionnaire::inRandomOrder()->first();
    }

    public static function getTotalQuestionnairesCount(): int
    {
        return Questionnaire::count();
    }

    public static function getLastInsertedRecord(): Questionnaire
    {
        return Questionnaire::query()
            ->orderByDesc('id')
            ->first();
    }

    public static function getRandomQuestionnaires(): Collection
    {
        return Questionnaire::query()
            ->inRandomOrder()
            ->limit(1)
            ->get();
    }
}
