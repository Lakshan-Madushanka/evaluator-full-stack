<?php

namespace App\Http\Controllers\Api\V1\Administrative\User\Questionnaire;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserQuestionnaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Vinkla\Hashids\Facades\Hashids;

class DetachQuestionnaireController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return JsonResponse|void
     *
     * @throws ValidationException
     */
    public function __invoke(User $user, string $userQuestionnaireId, Request $request)
    {
        $decodedQuestionnaireId = Hashids::decode($userQuestionnaireId)[0] ?? null;

        $questionnaire = UserQuestionnaire::query()
            ->where([
                ['id', '=', $decodedQuestionnaireId],
                ['user_id', '=', $user->id],
            ])->firstOrFail();

        if ($questionnaire->attempts > 0) {
            throw ValidationException::withMessages(['user_questionnaire' => __('can not detach already attempted questionnaire')]);
        }

        $questionnaire->delete();
    }
}
