<?php

namespace App\Http\Controllers\Api\V1\Administrative\Dashboard;

use App\Enums\Difficulty;
use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\JsonResponse;

class QuestionController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $easyValue = Difficulty::EASY->value;
        $mediumValue = Difficulty::MEDIUM->value;
        $hardValue = Difficulty::HARD->value;

        $data = Question::query()
            ->selectRaw("count(case when difficulty = {$easyValue} then 1 end) as no_of_easy_questions")
            ->selectRaw("count(case when difficulty = {$mediumValue} then 1 end) as no_of_medium_questions")
            ->selectRaw("count(case when difficulty = {$hardValue} then 1 end) as no_of_hard_questions")
            ->selectRaw('count(1) as no_of_total_questions')
            ->toBase()
            ->get();

        return new JsonResponse(data: $data);
    }
}
