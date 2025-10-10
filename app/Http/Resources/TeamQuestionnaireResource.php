<?php

namespace App\Http\Resources;

use App\Models\Team;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @mixin Team
 */
class TeamQuestionnaireResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'team_questionnaire_id' => Hashids::encode($this->pivot->id),
            'questionnaire_id' => $this->id,
            'questionnaire_name' => $this->name,
            'total_users' => $this->total_users,
            'attempted_users' => $this->attempted_users_count,
            'created_at' => $this->pivot->created_at,
            'updated_at' => $this->pivot->created_at,

        ];
    }
}
