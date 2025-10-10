<?php

namespace App\Http\Controllers\Api\V1\Administrative\Questionnaire;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionnaireResource;
use App\Models\Questionnaire;

class ShowQuestionnaireController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Questionnaire $questionnaire): QuestionnaireResource
    {
        $questionnaire->load(['categories'])->loadCount(['questions']);

        return new QuestionnaireResource($questionnaire);
    }
}
