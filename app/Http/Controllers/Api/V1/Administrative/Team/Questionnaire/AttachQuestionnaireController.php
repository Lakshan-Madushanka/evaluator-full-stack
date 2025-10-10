<?php

namespace App\Http\Controllers\Api\V1\Administrative\Team\Questionnaire;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\UserQuestionnaire;
use App\Notifications\QuestionnaireAttachedToUser;
use App\Services\QuestionnaireService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttachQuestionnaireController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return JsonResponse|void
     *
     * @throws \Throwable
     */
    public function __invoke(Team $team, string $questionnaireId, Request $request, QuestionnaireService $questionnaireService)
    {

        return DB::transaction(function () use ($team, $questionnaireId, $request, $questionnaireService) {
            if (! ($questionnaire = $questionnaireService->checkAvailability($questionnaireId))) {
                return $questionnaireService->ineligibleResponse();
            }

            $questionnaireId = $questionnaireService->decodeId($questionnaireId);

            $team->questionnaires()->attach($questionnaire);

            $teamQuestionnaire = \DB::table('questionnaire_team')
                ->where('questionnaire_id', $questionnaireId)
                ->where('team_id', $team->id)
                ->orderByDesc('id')
                ->first();

            $users = $team->users;

            if ($users->count() === 0) {
                return -1;
            }

            $userQuestionnaireRecords = [];

            foreach ($users as $user) {
                ['code' => $code, 'expires_at' => $expiresAt] = $questionnaireService->getAttributes($questionnaire);

                $userQuestionnaireRecords[] = [
                    'questionnaire_team_id' => $teamQuestionnaire->id,
                    'user_id' => $user->id,
                    'questionnaire_id' => $questionnaire->id,
                    'code' => $code,
                    'expires_at' => $expiresAt,
                ];
            }

            DB::table((new UserQuestionnaire)->getTable())->insert($userQuestionnaireRecords);

            foreach ($users as $user) {
                $user->notify(new QuestionnaireAttachedToUser($code, $request->action_url));
            }
        });
    }
}
