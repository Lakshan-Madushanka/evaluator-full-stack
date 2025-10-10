<?php

namespace App\Http\Resources;

use App\Models\Question;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @mixin Question
 */
class QuestionResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        $attributes = [
            'pretty_id' => $this->pretty_id,
            'hardness' => $this->difficulty->name,
            'content' => $this->text,
            'created_at' => $this->created_at,
            'answers_type_single' => $this->is_answers_type_single,
            'no_of_answers' => $this->no_of_answers,
            'completed' => $this->no_of_answers === $this->whenCounted('answers'),
            'no_of_assigned_answers' => $this->whenCounted('answers'),
            'images_count' => $this->whenCounted('images'),
        ];

        if (isset($this->pivot?->marks)) {
            $attributes['marks'] = $this->pivot->marks;
        }

        return $attributes;
    }

    public function toRelationships(Request $request): array
    {
        return [
            'images' => fn () => MediaResource::collection($this->images),
            'categories' => fn () => CategoryResource::collection($this->categories),
            'answers' => fn () => AnswerResource::collection($this->answers),
            'onlyAnswers' => fn () => AnswerResource::collection($this->onlyAnswers),
        ];
    }
}
