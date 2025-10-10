<?php

namespace App\Http\Controllers\Api\V1\Administrative\Answer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Answer\AnswerRequest;
use App\Http\Resources\AnswerResource;
use App\Models\Answer;

class UpdateAnswerController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Answer $answer, AnswerRequest $request): AnswerResource
    {
        /** @var array<string> $validatedInputs * */
        $validatedInputs = $request->validated();

        $answer->update($validatedInputs);

        return new AnswerResource($answer);
    }
}
