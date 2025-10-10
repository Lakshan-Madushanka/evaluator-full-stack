<?php

namespace App\Http\Controllers\Api\V1\Administrative\Question;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Question\QuestionUpdateRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Support\Arr;

class UpdateQuestionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  UserStoreRequest  $request
     */
    public function __invoke(Question $question, QuestionUpdateRequest $request): QuestionResource
    {
        /** @var array<string> $validatedInputs * */
        $validatedInputs = $request->validated();

        $question->update(Arr::except($validatedInputs, 'categories'));

        $question->categories()->sync(Helpers::getModelIdsFromHashIds($validatedInputs['categories']));

        return new QuestionResource($question);
    }
}
