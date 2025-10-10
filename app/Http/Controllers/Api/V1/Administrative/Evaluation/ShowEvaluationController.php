<?php

namespace App\Http\Controllers\Api\V1\Administrative\Evaluation;

use App\Http\Resources\EvaluationResource;
use App\Models\Evaluation;

class ShowEvaluationController
{
    public function __invoke(Evaluation $evaluation): EvaluationResource
    {
        $evaluation = $evaluation->load('userQuestionnaire');

        return new EvaluationResource($evaluation);
    }
}
