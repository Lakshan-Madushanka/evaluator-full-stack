<?php

namespace App\Http\Controllers\Api\V1\Administrative\User\Questionnaire;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserQuestionnaireResource;
use App\Models\Questionnaire;
use App\Models\User;
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
    public function __invoke(User $user, Request $request): JsonApiResourceCollection
    {
        $questionnaires = QueryBuilder::for($user->questionnaires())
            ->select([
                'questionnaires.id',
                'user_questionnaire.id as userQuestionnaireId',
                'user_questionnaire.questionnaire_team_id',
                'user_questionnaire.code',
                'user_questionnaire.started_at',
                'user_questionnaire.finished_at',
                'user_questionnaire.attempts',
                'user_questionnaire.expires_at',
                'user_questionnaire.updated_at as user_questionnaire_updated_at',
                'user_questionnaire.created_at as user_questionnaire_created_at',
            ])
            ->allowedFilters([
                AllowedFilter::callback('uq_id', function (Builder $query, $value) {
                    $id = Hashids::decode($value)[0] ?? null;

                    return $query->where((new UserQuestionnaire)->qualifyColumn('id'), $id);
                }),
                AllowedFilter::callback('attempted', function (Builder $query, $value) {
                    if (Helpers::checkValueIsTrue($value)) {
                        return $query->where('user_questionnaire.attempts', '>', 0);
                    }

                    return $query->where('user_questionnaire.attempts', 0);
                }),
                AllowedFilter::callback('expired', function (Builder $query, $value) {
                    if (Helpers::checkValueIsTrue($value)) {
                        return $query->where('user_questionnaire.expires_at', '<=', now());
                    }

                    return $query->where('user_questionnaire.expires_at', '>=', now());
                }),
                AllowedFilter::callback('id', function (Builder $query, $value) {
                    $id = Hashids::decode($value)[0] ?? null;

                    return $query->where((new Questionnaire)->qualifyColumn('id'), $id);
                }),
            ])
            ->defaultSort('-user_questionnaire.id')
            ->allowedSorts([
                AllowedSort::field('created_at', 'user_questionnaire.created_at'),
            ])
            ->jsonPaginate();
        
        return UserQuestionnaireResource::collection($questionnaires);
    }
}
