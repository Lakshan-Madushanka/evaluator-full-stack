<?php

namespace App\Http\Controllers\Api\V1\Administrative\Question;

use App\Enums\Difficulty;
use App\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use ReflectionEnumBackedCase;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class IndexQuestionController extends Controller
{
    public function __invoke(Request $request): JsonApiResourceCollection
    {
        $questionsQuery = QueryBuilder::for(Question::class)
            ->withCount(['answers', 'images'])
            ->allowedIncludes(['categories'])
            ->allowedFilters([
                AllowedFilter::callback('content', function (Builder $query, $value) {
                    $query->whereFullText('text', $value);
                }),
                AllowedFilter::callback('hardness', function (Builder $query, $value) {
                    $query->where(
                        'difficulty',
                        // This will return enum value by its name ex: SUPER_ADMIN return 1
                        (new ReflectionEnumBackedCase(Difficulty::class, $value))->getBackingValue()
                    );
                }),
                AllowedFilter::callback('completed', function (Builder $query, $value) {
                    if (Helpers::checkValueIsTrue($value)) {
                        $query->havingRaw('no_of_answers = answers_count');
                    } else {
                        $query->havingRaw('no_of_answers <> answers_count');
                    }
                }),
                AllowedFilter::exact('pretty_id'),
                AllowedFilter::exact('answers_type_single', 'is_answers_type_single'),
                'categories.name',
            ]);

        if (! Arr::has($request->query(), 'filter.content')) {
            $questionsQuery->defaultSort('-id');
        }

        $questions = $questionsQuery->allowedSorts('created_at')
            ->jsonPaginate();

        return QuestionResource::collection($questions);
    }
}
