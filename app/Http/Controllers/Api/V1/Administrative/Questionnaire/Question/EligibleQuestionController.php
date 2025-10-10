<?php

namespace App\Http\Controllers\Api\V1\Administrative\Questionnaire\Question;

use App\Enums\Difficulty;
use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\Questionnaire;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReflectionEnumBackedCase;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class EligibleQuestionController extends Controller
{
    public function find(Questionnaire $questionnaire, string $questionId, Request $request): QuestionResource|JsonResponse
    {
        $question = Question::query()
            ->eligible($questionnaire)
            ->wherePrettyId($questionId)
            ->withCount('images')
            ->first();

        if (is_null($question)) {
            return new JsonResponse(data: ['eligible' => false]);
        }

        return new QuestionResource($question);
    }

    public function index(Questionnaire $questionnaire): JsonApiResourceCollection
    {
        $qIds = $questionnaire->questions()->pluck((new Question)->qualifyColumn('id'));

        // return $qIds->toArray();

        $questions = QueryBuilder::for(Question::query())
            ->eligible($questionnaire)
            ->whereNotIn('id', $qIds->toArray())
            ->withCount('images')
            ->with('categories')
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
                AllowedFilter::exact('pretty_id'),
                AllowedFilter::exact('answers_type_single', 'is_answers_type_single'),
                'categories.name',
            ])
            ->defaultSort('-id')
            ->allowedSorts('created_at')
            ->jsonPaginate();

        return QuestionResource::collection($questions);

    }
}
