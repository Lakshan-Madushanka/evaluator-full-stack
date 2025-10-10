<?php

namespace App\Http\Requests\Questionnaire;

use App\Enums\Difficulty;
use App\Http\Requests\Contracts\RequestValidationRulesContract;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class QuestionnaireRequestValidationRules implements RequestValidationRulesContract
{
    /**
     * @return array<string, mixed>
     */
    public static function getRules(Request $request): array
    {
        return [
            'name' => ['required', 'string', 'between:3,50'],
            'difficulty' => ['required', new Enum(Difficulty::class)],
            'single_answers_type' => ['required', 'boolean'],
            'no_of_easy_questions' => ['required', 'integer'],
            'no_of_medium_questions' => ['required', 'integer'],
            'no_of_hard_questions' => ['required', 'integer'],
            'no_of_questions' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    $totalQuestions = (int) $request->no_of_easy_questions +
                        (int) $request->no_of_medium_questions +
                        (int) $request->no_of_hard_questions;

                    if ($value !== $totalQuestions) {
                        $fail('The '.$attribute.' is invalid.');
                    }
                },
            ],
            'allocated_time' => ['required', 'integer'],
            'categories' => ['array', 'required'],
            'categories.*' => ['string', 'required'],
        ];
    }
}
