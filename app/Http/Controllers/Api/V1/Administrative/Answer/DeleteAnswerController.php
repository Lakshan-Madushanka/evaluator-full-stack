<?php

namespace App\Http\Controllers\Api\V1\Administrative\Answer;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteAnswerController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Answer $question): JsonResponse
    {
        $question->delete();

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
