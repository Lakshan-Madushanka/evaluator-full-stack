<?php

namespace App\Http\Controllers\Api\V1\Administrative\Questionnaire;

use App\Http\Controllers\Controller;
use App\Models\Questionnaire;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteQuestionnaireController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Questionnaire $questionnaire): JsonResponse
    {
        $questionnaire->delete();

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
