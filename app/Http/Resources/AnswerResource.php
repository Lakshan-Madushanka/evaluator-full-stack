<?php

namespace App\Http\Resources;

use App\Models\Answer;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @mixin Answer
 */
class AnswerResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        $attributes = [
            'pretty_id' => $this->pretty_id,
            'text' => $this->text,
            'created_at' => $this->created_at,
            'images_count' => $this->images_count ?? 0,
        ];
        if (isset($this->pivot?->correct_answer)) {
            $attributes['correct_answer'] = (bool) $this->pivot->correct_answer;
        }

        return $attributes;
    }

    public function toRelationships(Request $request): array
    {
        return [
            'images' => fn () => MediaResource::collection($this->images),
        ];
    }
}
