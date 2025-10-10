<?php

namespace App\Http\Controllers\Api\V1\Administrative\Questionnaire;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Questionnaire\QuestionnaireStoreRequest;
use App\Http\Resources\QuestionnaireResource;
use App\Models\Questionnaire;
use Illuminate\Support\Arr;

class StoreQuestionnaireController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(QuestionnaireStoreRequest $request): QuestionnaireResource
    {
        /** @var array<string> $validatedInputs * */
        $validatedInputs = $request->validated();

        $questionnaire = Questionnaire::create(Arr::except($validatedInputs, 'categories'));

        $questionnaire->categories()->attach(Helpers::getModelIdsFromHashIds($validatedInputs['categories']));

        return new QuestionnaireResource($questionnaire);
    }
}
