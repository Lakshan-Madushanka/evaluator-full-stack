<?php

namespace App\Http\Controllers\Api\V1\Administrative\Question\Answer;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnswerResource;
use App\Models\Question;
use Illuminate\Http\Request;

class IndexAnswerController extends Controller
{
    public function __invoke(Question $question, Request $request): \TiMacDonald\JsonApi\JsonApiResourceCollection
    {
        $answers = $question->answers()->withCount('images')->get();

        return AnswerResource::collection($answers);
    }
}
