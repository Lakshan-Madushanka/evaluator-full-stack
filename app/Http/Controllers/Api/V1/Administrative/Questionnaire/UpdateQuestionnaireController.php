<?php

namespace App\Http\Controllers\Api\V1\Administrative\Questionnaire;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Question\QuestionUpdateRequest;
use App\Http\Requests\Questionnaire\QuestionnaireUpdateRequest;
use App\Http\Resources\QuestionnaireResource;
use App\Models\Questionnaire;
use Illuminate\Support\Arr;

class UpdateQuestionnaireController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  QuestionUpdateRequest  $request
     */
    public function __invoke(Questionnaire $questionnaire, QuestionnaireUpdateRequest $request): QuestionnaireResource
    {
        /** @var array<string> $validatedInputs * */
        $validatedInputs = $request->validated();

        $questionnaire->update(Arr::except($validatedInputs, 'categories'));

        $questionnaire->categories()->sync(Helpers::getModelIdsFromHashIds($validatedInputs['categories']));

        return new QuestionnaireResource($questionnaire);
    }
}
