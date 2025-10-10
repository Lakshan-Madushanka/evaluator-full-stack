<?php

namespace App\Http\Controllers\Api\V1\Administrative\Team\Questionnaire;

use App\Http\Controllers\Controller;
use App\Models\Questionnaire;
use App\Models\Team;
use App\Models\User;
use App\Models\UserQuestionnaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;

class DetachQuestionnaireController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return Response
     *
     * @throws ValidationException|Throwable
     */
    public function __invoke(Team $team, Questionnaire $questionnaire, Request $request)
    {
        throw_if(
            $team->userQuestionnaires()->where('attempts', '>', 0)->exists(),
            ValidationException::withMessages(['user_questionnaire' => __('can not detach already attempted questionnaire')])
        );

        $team->questionnaires()->detach([$questionnaire->id]);

        return response()->noContent();
    }
}
