<?php

namespace App\Http\Controllers\Api\V1\Administrative\Team\Questionnaire\User;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserQuestionnaireResource;
use App\Http\Sorts\UserQuestionnaire\MarkSort;
use App\Models\QuestionnaireTeam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\SortDirection;
use Spatie\QueryBuilder\QueryBuilder;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class IndexUserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(QuestionnaireTeam $questionnaireTeam, Request $request): JsonApiResourceCollection
    {
        $markSort = AllowedSort::custom('marks', new MarkSort)->defaultDirection(SortDirection::DESCENDING);

        $questionnaires = QueryBuilder::for($questionnaireTeam
            ->users()
            ->with(['evaluation', 'user'])
        )
            ->select([
                'user_questionnaire.id',
                'user_questionnaire.user_id',
                'user_questionnaire.questionnaire_team_id',
                'user_questionnaire.id as userQuestionnaireId',
                'user_questionnaire.code',
                'user_questionnaire.started_at',
                'user_questionnaire.finished_at',
                'user_questionnaire.attempts',
                'user_questionnaire.expires_at',
                'user_questionnaire.updated_at as user_questionnaire_updated_at',
                'user_questionnaire.created_at as user_questionnaire_created_at',
            ])
            ->allowedFilters([
                AllowedFilter::callback('name', function (Builder $query, $value) {
                    return $query->whereHas('user', function (Builder $query) use ($value) {
                        return $query->where('name', 'LIKE', "%{$value}%");
                    });
                }),
                AllowedFilter::callback('email', function (Builder $query, $value) {
                    return $query->whereHas('user', function (Builder $query) use ($value) {
                        return $query->where('email', 'LIKE', "%{$value}%");
                    });
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
            ])
            ->defaultSort($markSort)
            ->allowedSorts([
                AllowedSort::field('created_at', 'user_questionnaire.created_at'),
                $markSort,
            ])
            ->jsonPaginate();

        return UserQuestionnaireResource::collection($questionnaires);
    }
}
