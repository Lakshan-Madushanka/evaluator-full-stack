<?php

namespace App\Http\Controllers\Api\V1\Administrative\Questionnaire;

use App\Http\Controllers\Controller;
use App\Models\Questionnaire;
use Illuminate\Http\JsonResponse;
use Vinkla\Hashids\Facades\Hashids;

class CheckQuestionnaireAvailableController extends Controller
{
    public function __invoke(string $questionnaireId): JsonResponse
    {
        $decodedQuestionnaireId = Hashids::decode($questionnaireId)[0] ?? null;

        $exists = false;

        if ($decodedQuestionnaireId) {
            $exists = Questionnaire::query()
                ->whereId($decodedQuestionnaireId)
                ->withCount('questions')
                ->completed(true)
                ->exists();
        }

        return new JsonResponse(data: ['available' => $exists]);
    }
}
