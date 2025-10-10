<?php

namespace App\Http\Controllers\Api\V1\Administrative\Question;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;

class ShowQuestionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  UserStoreRequest  $request
     * @return QuestionResource
     */
    public function __invoke(Question $question)
    {
        $question->load(['categories'])->loadCount(['answers', 'images']);

        return new QuestionResource($question);
    }
}
