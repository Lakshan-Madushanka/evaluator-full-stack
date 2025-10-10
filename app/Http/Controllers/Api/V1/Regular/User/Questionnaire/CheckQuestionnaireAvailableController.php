<?php

namespace App\Http\Controllers\Api\V1\Regular\User\Questionnaire;

use App\Http\Controllers\Controller;
use App\Models\Questionnaire;
use App\Models\UserQuestionnaire;
use Illuminate\Http\JsonResponse;

class CheckQuestionnaireAvailableController extends Controller
{
    public function __invoke(string $code): JsonResponse
    {
        $userQuestionnaire = UserQuestionnaire::query()
            ->available($code)
            ->first();

        if (is_null($userQuestionnaire)) {
            return new JsonResponse(data: ['available' => false]);
        }

        $questionnaire = Questionnaire::whereId($userQuestionnaire->questionnaire_id)->firstOrFail();

        return new JsonResponse([
            'name' => $questionnaire->name,
            'single_answer_type' => $questionnaire->single_answers_type,
            'no_of_questions' => $questionnaire->no_of_questions,
            'allocated_time' => $questionnaire->allocated_time,
            'expires_at' => $userQuestionnaire->expires_at,
        ]);
    }
}
