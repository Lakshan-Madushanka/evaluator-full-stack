<?php

namespace Tests\Repositories;

use App\Models\Evaluation;
use App\Models\UserQuestionnaire;

class EvaluationRepository
{
    public static function createEvaluations(int $limit = 25): void
    {
        $userQuestionnaires = UserQuestionnaire::query()
            ->limit($limit)
            ->get()
            ->each(function (UserQuestionnaire $uq) {
                Evaluation::create([
                    'user_questionnaire_id' => $uq->id,
                    'time_taken' => random_int(25, 60),
                    'correct_answers' => random_int(5, 30),
                    'no_of_answered_questions' => random_int(20, 30),
                    'marks_percentage' => random_int(10, 100),
                    'total_points_earned' => random_int(0, 10),
                    'total_points_allocated' => random_int(10, 20),
                ]);
            });
    }

    public static function getRandomEvaluation(): Evaluation
    {
        return Evaluation::inRandomOrder()->first();
    }
}
