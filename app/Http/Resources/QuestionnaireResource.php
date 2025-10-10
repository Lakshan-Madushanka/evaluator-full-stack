<?php

namespace App\Http\Resources;

use App\Models\Question;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @mixin Question
 */
class QuestionnaireResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        $attributes = [
            'name' => $this->name,
            'difficulty' => $this->difficulty->name,
            'single_answers_type' => $this->single_answers_type,
            'no_of_easy_questions' => $this->no_of_easy_questions,
            'no_of_medium_questions' => $this->no_of_medium_questions,
            'no_of_hard_questions' => $this->no_of_hard_questions,
            'no_of_questions' => $this->no_of_questions,
            'no_of_assigned_questions' => $this->whenCounted('questions'),
            'allocated_time' => $this->allocated_time,
            'created_at' => $this->created_at,
            'completed' => $this->no_of_questions === $this->whenCounted('questions'),
        ];

        if (isset($this->answers)) {
            $attributes['answers'] = $this->answers;
        }

        return $attributes;
    }

    public function toRelationships(Request $request): array
    {
        return [
            'categories' => fn () => CategoryResource::collection($this->categories),
        ];
    }
}
