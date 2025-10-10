<?php

namespace App\Http\Controllers\Api\V1\Administrative\Answer;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnswerResource;
use App\Models\Answer;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class IndexAnswerController extends Controller
{
    public function __invoke(Request $request): JsonApiResourceCollection
    {
        $answersQuery = QueryBuilder::for(Answer::class)
            ->withCount(['images'])
            ->allowedFilters([
                AllowedFilter::callback('text', function (Builder $query, $value) {
                    $query->whereFullText('text', $value);
                }),
                AllowedFilter::exact('pretty_id'),
            ]);

        if (! Arr::has($request->query(), 'filter.text')) {
            $answersQuery->defaultSort('-id');
        }

        $answers = $answersQuery
            ->allowedSorts('created_at')
            ->jsonPaginate();

        return AnswerResource::collection($answers);
    }
}
