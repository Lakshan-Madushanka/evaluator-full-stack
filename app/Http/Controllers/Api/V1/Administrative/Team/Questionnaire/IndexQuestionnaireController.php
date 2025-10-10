<?php

namespace App\Http\Controllers\Api\V1\Administrative\Team\Questionnaire;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamQuestionnaireResource;
use App\Models\Questionnaire;
use App\Models\Team;
use App\Models\UserQuestionnaire;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use TiMacDonald\JsonApi\JsonApiResourceCollection;
use Vinkla\Hashids\Facades\Hashids;

class IndexQuestionnaireController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Team $team, Request $request): JsonApiResourceCollection
    {
        $questionnaires = QueryBuilder::for(
            $team->questionnaires()
                ->addSelect(['total_users' => UserQuestionnaire::query()
                    ->selectRaw('COUNT(1)')
                    ->whereColumn('questionnaire_team.id', 'user_questionnaire.questionnaire_team_id')
                    ->limit(1),
                ])
                ->addSelect(['attempted_users_count' => UserQuestionnaire::query()
                    ->selectRaw('COUNT(1)')
                    ->whereColumn('questionnaire_team.id', 'user_questionnaire.questionnaire_team_id')
                    ->where('attempts', '>', 0)
                    ->limit(1),
                ])
        )
            ->allowedFilters([
                AllowedFilter::callback('id', function (Builder $query, $value) {
                    $id = Hashids::decode($value)[0] ?? null;

                    return $query->where((new Questionnaire)->qualifyColumn('id'), $id);
                }),
                'name',
            ])
            ->defaultSort('-questionnaire_team.created_at')
            ->allowedSorts([
                AllowedSort::field('created_at', 'questionnaire_team.created_at'),
            ])
            ->jsonPaginate();

        return TeamQuestionnaireResource::collection($questionnaires);
    }
}
