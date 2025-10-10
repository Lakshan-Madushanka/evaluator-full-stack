<?php

namespace App\Http\Resources;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use TiMacDonald\JsonApi\JsonApiResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @mixin Question
 */
class UserQuestionnaireResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        $attributes = [
            'user_questionnaire_id' => $this->when(isset($this->userQuestionnaireId), fn () => Hashids::encode($this->userQuestionnaireId)),
            'user_questionnaire_team_id' => $this->when(isset($this->questionnaire_team_id), fn () => Hashids::encode($this->questionnaire_team_id)),
            'is_team_attached' => ! is_null($this->questionnaire_team_id),
            'user_id' => $this->when(isset($this->user_id), fn () => Hashids::encode($this->user_id)),
            'code' => $this->when(isset($this->code), fn () => $this->code),
            'attempts' => $this->attempts,
            'expires_at' => $this->expires_at,
            'started_at' => $this->started_at,
            'finished_at' => $this->finished_at,
            'created_at' => Carbon::parse($this->user_questionnaire_created_at),
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
            'evaluation' => fn () => new EvaluationResource($this->evaluation),
            'user' => fn () => new UserResource($this->user),
        ];
    }
}
