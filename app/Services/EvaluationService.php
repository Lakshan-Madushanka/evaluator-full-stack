<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Questionnaire;
use Illuminate\Database\Eloquent\Collection;

class EvaluationService
{
    public static function evaluate(Questionnaire $questionnaire, \Illuminate\Support\Collection $answers): array
    {
        $questions = $questionnaire->questionsWithPivotData()->with('answers')->get()->keyBy('id');
        $answers = $answers->filter();

        $totalMarks = 0;
        $noOfAnsweredQuestions = 0;
        $noOfCorrectAnswers = 0;
        $questionnaireTotalMarks = 0;

        $questions->each(function (Question $question) use (
            $answers,
            &$totalMarks,
            &$noOfAnsweredQuestions,
            &$noOfCorrectAnswers,
            &$questionnaireTotalMarks
        ) {
            $questionnaireTotalMarks += $question->pivot->marks;

            $answerExists = $answers->contains(fn ($value, $key) => $key === $question->id);

            if ($answerExists) {
                $noOfAnsweredQuestions++;

                $totalMarks += self::evaluateMarksPerQuestion(
                    self::getCorrectAnswersIds($question->answers),
                    collect($answers[$question->id]),
                    $question->pivot->marks,
                    $noOfCorrectAnswers,
                );
            }
        });

        $marksPercentage = round(($totalMarks / $questionnaireTotalMarks) * 100, 1);

        return [
            'marksPercentage' => $marksPercentage,
            'totalPointsEarned' => round($totalMarks, 1),
            'totalPointsAllocated' => round($questionnaireTotalMarks, 1),
            'noOfAnsweredQuestions' => $noOfAnsweredQuestions,
            'noOfCorrectAnswers' => $noOfCorrectAnswers,
        ];
    }

    public static function getCorrectAnswersIds(Collection $answers): \Illuminate\Support\Collection
    {
        return $answers->filter(fn (Answer $answer) => (bool) $answer->pivot->correct_answer)->pluck('id');
    }

    public static function evaluateMarksPerQuestion(
        \Illuminate\Support\Collection $correctAnswers,
        \Illuminate\Support\Collection $userAnswers,
        $marks,
        &$noOfCorrectAnswers
    ): int|float {
        $earnedMarks = 0;
        $markPerCorrectAnswer = $marks / $correctAnswers->count();

        $userAnswers = $userAnswers->unique();

        $userAnswers->each(function (int $userAnswerId) use (&$earnedMarks, $markPerCorrectAnswer, $correctAnswers) {
            if ($correctAnswers->contains($userAnswerId)) {
                $earnedMarks += $markPerCorrectAnswer;
            } else {
                $earnedMarks -= $markPerCorrectAnswer;
            }
        });

        if ($earnedMarks < 0) {
            return 0;
        }

        if ($earnedMarks === $marks) {
            $noOfCorrectAnswers++;
        }

        return $earnedMarks;
    }
}
