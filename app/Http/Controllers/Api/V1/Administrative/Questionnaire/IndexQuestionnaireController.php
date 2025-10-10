<?php

namespace App\Http\Controllers\Api\V1\Administrative\Questionnaire;

use App\Enums\Difficulty;
use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionnaireResource;
use App\Models\Questionnaire;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use ReflectionEnumBackedCase;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class IndexQuestionnaireController extends Controller
{
    public function __invoke(Request $request): JsonApiResourceCollection
    {
        $questions = QueryBuilder::for(Questionnaire::class)
            ->allowedIncludes(['categories'])
            ->withCount(['questions'])
            ->allowedFilters([
                'name',
                AllowedFilter::callback('difficulty', function (Builder $query, $value) {
                    $query->where(
                        'difficulty',
                        // This will return enum value by its name ex: SUPER_ADMIN return 1
                        (new ReflectionEnumBackedCase(Difficulty::class, $value))->getBackingValue()
                    );
                }),
                AllowedFilter::callback('no_of_easy_questions', function (Builder $query, $value) {
                    $query->whereBetween('no_of_easy_questions', [$value]);
                }),
                AllowedFilter::callback('no_of_medium_questions', function (Builder $query, $value) {
                    $query->whereBetween('no_of_medium_questions', [$value]);
                }),
                AllowedFilter::callback('no_of_hard_questions', function (Builder $query, $value) {
                    $query->whereBetween('no_of_hard_questions', [$value]);
                }),
                AllowedFilter::callback('no_of_questions', function (Builder $query, $value) {
                    $query->whereBetween('no_of_questions', [$value]);
                }),
                AllowedFilter::callback('allocated_time', function (Builder $query, $value) {
                    $query->whereBetween('allocated_time', [$value]);
                }),
                AllowedFilter::exact('single_answers_type'),
                AllowedFilter::scope('completed'),
                'categories.name',
            ])
            ->defaultSort('-id')
            ->allowedSorts('created_at')
            ->jsonPaginate();

        return QuestionnaireResource::collection($questions);
    }
}
