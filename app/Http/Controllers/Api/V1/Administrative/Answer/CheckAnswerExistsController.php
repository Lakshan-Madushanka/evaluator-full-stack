<?php

namespace App\Http\Controllers\Api\V1\Administrative\Answer;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Resources\AnswerResource;
use App\Models\Answer;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CheckAnswerExistsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  UserStoreRequest  $request
     */
    public function __invoke(string $id): JsonResponse
    {
        $answer = Answer::wherePrettyId($id)->first();

        return new JsonResponse(
            [
                'exists' => ! is_null($answer),
                'answer' => $answer ? new AnswerResource($answer) : null,
            ],
            status: Response::HTTP_OK);
    }
}
