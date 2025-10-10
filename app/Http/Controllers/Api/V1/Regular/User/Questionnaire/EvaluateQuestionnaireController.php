<?php

namespace App\Http\Controllers\Api\V1\Regular\User\Questionnaire;

use App\Exceptions\EvaluationException;
use App\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\EvaluationResource;
use App\Models\Evaluation;
use App\Models\Questionnaire;
use App\Models\UserQuestionnaire;
use App\Services\EvaluationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;

class EvaluateQuestionnaireController extends Controller
{
    /**
     * @throws Throwable
     * @throws EvaluationException
     */
    public function __invoke(string $code, Request $request)
    {
        /**
         * 1). Check questionnaire availability (Check user has finished questionnaire within allocated time from
         * started time).
         * 2). Check finished at column if so, return marks
         * 3). Set finished at column
         * 4). validate answers
         * 3). Convert answers hashIds to model ids.
         * 4). store answers and time taken
         * 5). Evaluate answers and update evaluator table
         * 6). return evaluation
         **/
        $userQuestionnaire = UserQuestionnaire::query()
            ->expired()
            ->where('code', $code)
            ->firstOrFail();

        if (! is_null($userQuestionnaire->finished_at)) {
            $evaluation = Evaluation::query()
                ->where('user_questionnaire_id', $userQuestionnaire->id)
                ->firstOrFail();

            return new EvaluationResource($evaluation);
        }

        $questionnaire = Questionnaire::query()
            ->findOrFail($userQuestionnaire->questionnaire_id);

        /** @var Carbon $startedAt */
        $startedAt = $userQuestionnaire->started_at;

        // Allows 5 minute grace time
        $allowedFinishedAtTime = $startedAt->clone()->addMinutes($questionnaire->allocated_time + 5);

        throw_if(
            now()->isAfter($allowedFinishedAtTime),
            EvaluationException::make(message: 'Exceeded max allowed time', code: 400)
        );

        $validatedInputs = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['array'],
        ]);

        $validatedAnswers = $this->convertToModelIds($validatedInputs['answers']);

        $userQuestionnaire->finished_at = now();
        $userQuestionnaire->answers = $validatedAnswers;
        $userQuestionnaire->save();

        $evaluation = EvaluationService::evaluate($questionnaire, collect($validatedAnswers));

        $evaluation = Evaluation::create([
            'user_questionnaire_id' => $userQuestionnaire->id,
            'time_taken' => $startedAt->diffInMinutes($userQuestionnaire->finished_at),
            'correct_answers' => $evaluation['noOfCorrectAnswers'],
            'no_of_answered_questions' => $evaluation['noOfAnsweredQuestions'],
            'marks_percentage' => $evaluation['marksPercentage'],
            'total_points_earned' => $evaluation['totalPointsEarned'],
            'total_points_allocated' => $evaluation['totalPointsAllocated'],
        ]);

        return new EvaluationResource($evaluation);
    }

    public function convertToModelIds(array $answers): array
    {
        $data = [];

        foreach ($answers as $key => $value) {
            $data[Hashids::decode($key)[0]] = Helpers::getModelIdsFromHashIds($value);
        }

        return $data;
    }
}
