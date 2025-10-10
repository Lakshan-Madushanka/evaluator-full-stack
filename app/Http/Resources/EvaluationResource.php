<?php

namespace App\Http\Resources;

use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use TiMacDonald\JsonApi\JsonApiResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @mixin Evaluation
 */
class EvaluationResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        $marksPercentage = round($this->marks_percentage, 2);

        $attributes = [
            'user_questionnaire_id' => Hashids::encode($this->user_questionnaire_id),
            'marks_percentage' => $marksPercentage,
            'total_points_earned' => $this->total_points_earned,
            'total_points_allocated' => $this->total_points_allocated,
            'time_taken' => $this->time_taken,
            'no_of_answered_questions' => $this->no_of_answered_questions,
            'no_of_correct_answers' => $this->correct_answers,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($this->relationLoaded('userQuestionnaire')) {
            $attributes['user_id'] = Hashids::encode($this->userQuestionnaire->user_id);
            $attributes['questionnaire_id'] = Hashids::encode($this->userQuestionnaire->questionnaire_id);

            if (isset($this->userQuestionnaire->answers)) {
                $attributes['answers'] = $this->convertAnswers(collect($this->userQuestionnaire->answers));
            }
        }

        return $attributes;
    }

    public function convertAnswers(Collection $answers): Collection
    {
        return $answers->mapWithKeys(callback: function (array $answers, string $key) {

            $answers = collect($answers)->transform(function (string $value) {
                return Hashids::encode($value);
            });

            return [Hashids::encode($key) => $answers];
        });
    }
}
