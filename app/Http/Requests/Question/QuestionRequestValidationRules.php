<?php

namespace App\Http\Requests\Question;

use App\Enums\Difficulty;
use App\Http\Requests\Contracts\RequestValidationRulesContract;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class QuestionRequestValidationRules implements RequestValidationRulesContract
{
    /**
     * @return array<string, mixed>
     */
    public static function getRules(Request $request): array
    {
        return [
            'difficulty' => ['required', new Enum(Difficulty::class)],
            'text' => ['string', 'required', 'min:3'],
            'is_answers_type_single' => ['boolean', 'required'],
            'no_of_answers' => ['integer', 'required', 'min:2'],
            'categories' => ['array', 'required'],
            'categories.*' => ['string', 'required'],
        ];
    }
}
