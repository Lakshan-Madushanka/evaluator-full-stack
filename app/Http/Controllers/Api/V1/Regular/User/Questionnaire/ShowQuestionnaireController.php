<?php

namespace App\Http\Controllers\Api\V1\Regular\User\Questionnaire;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Questionnaire;
use App\Models\UserQuestionnaire;
use Spatie\QueryBuilder\QueryBuilder;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class ShowQuestionnaireController extends Controller
{
    public function __invoke(string $code): JsonApiResourceCollection
    {
        $userQuestionnaire = UserQuestionnaire::query()
            ->available($code)
            ->first();

        if (is_null($userQuestionnaire)) {
            return QuestionResource::collection([]);
        }

        $questionnaire = Questionnaire::query()
            ->where('id', $userQuestionnaire->questionnaire_id)
            ->first();

        $questions = QueryBuilder::for($questionnaire?->questions())
            ->inRandomOrder()
            ->allowedIncludes(['images', 'onlyAnswers.images'])
            ->get();

        if ($userQuestionnaire->attempts === 0 && is_null($userQuestionnaire->started_at)) {
            $userQuestionnaire->attempts = 1;
            $userQuestionnaire->started_at = now();

            $userQuestionnaire->save();
        }

        return QuestionResource::collection($questions);
    }
}
