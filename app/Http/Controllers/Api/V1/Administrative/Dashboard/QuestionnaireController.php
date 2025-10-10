<?php

namespace App\Http\Controllers\Api\V1\Administrative\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Questionnaire;
use Illuminate\Http\JsonResponse;

class QuestionnaireController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $data = Questionnaire::query()
            ->selectRaw('max(no_of_easy_questions) as max_no_of_easy_questions_per_questionnaire')
            ->selectRaw('max(no_of_medium_questions) as max_no_of_medium_questions_per_questionnaire')
            ->selectRaw('max(no_of_hard_questions) as max_no_of_hard_questions_per_questionnaire')
            ->selectRaw('max(no_of_questions) as max_no_of_total_questions_per_questionnaire')
            ->selectRaw('max(allocated_time) as max_allocated_time_per_questionnaire')
            ->toBase()
            ->get();

        return new JsonResponse(data: $data[0]);
    }
}
