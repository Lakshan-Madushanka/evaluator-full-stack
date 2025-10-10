<?php

namespace App\Http\Controllers\Api\V1\Administrative\Answer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Answer\AnswerRequest;
use App\Http\Resources\AnswerResource;
use App\Models\Answer;

class StoreAnswerController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(AnswerRequest $request): AnswerResource
    {
        /** @var array<string> $validatedInputs * */
        $validatedInputs = $request->validated();

        $answer = Answer::create($validatedInputs);

        return new AnswerResource($answer);
    }
}
