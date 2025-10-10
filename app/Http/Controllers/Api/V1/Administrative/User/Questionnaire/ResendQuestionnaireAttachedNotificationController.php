<?php

namespace App\Http\Controllers\Api\V1\Administrative\User\Questionnaire;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserQuestionnaire;
use App\Notifications\QuestionnaireAttachedToUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class ResendQuestionnaireAttachedNotificationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  string  $questionnaireId
     * @return JsonResponse|void
     */
    public function __invoke(User $user, string $userQuestionnaireId, Request $request)
    {
        $id = Hashids::decode($userQuestionnaireId)[0] ?? null;

        $userQuestionnaire = UserQuestionnaire::query()
            ->where([
                ['id', $id],
                ['user_id', $user->id],
            ])
            ->checkAvailable()
            ->firstOrFail();

        $user->notify(new QuestionnaireAttachedToUser($userQuestionnaire->code, $request->action_url));
    }
}
