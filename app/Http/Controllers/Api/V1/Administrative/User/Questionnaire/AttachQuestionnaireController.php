<?php

namespace App\Http\Controllers\Api\V1\Administrative\User\Questionnaire;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\QuestionnaireAttachedToUser;
use App\Services\QuestionnaireService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttachQuestionnaireController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return JsonResponse|void
     */
    public function __invoke(User $user, string $questionnaireId, Request $request, QuestionnaireService $questionnaireService)
    {
        $questionnaire = $questionnaireService->checkAvailability($questionnaireId);

        if (! $questionnaire) {
            return $questionnaireService->ineligibleResponse();
        }

        ['code' => $code, 'expires_at' => $expiresAt] = $questionnaireService->getAttributes($questionnaire);

        $user->questionnaires()->attach($questionnaireService->decodeId($questionnaireId), ['code' => $code, 'expires_at' => $expiresAt]);

        $user->notify(new QuestionnaireAttachedToUser($code, $request->action_url));
    }
}
