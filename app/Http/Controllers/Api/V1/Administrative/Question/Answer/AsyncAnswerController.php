<?php

namespace App\Http\Controllers\Api\V1\Administrative\Question\Answer;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

class AsyncAnswerController extends Controller
{
    public function __invoke(Question $question, Request $request): JsonResponse
    {
        $validatedInputs = $request->validate([
            'answers' => ['array'],
            'answers.*.id' => ['required', 'string'],
            'answers.*.correct' => ['required', 'boolean'],
        ]);

        if (count($validatedInputs['answers']) !== 0) {
            $this->checkValidity($validatedInputs, $question);
        }

        $ids = $this->prepareInputs($validatedInputs);

        $results = $question->answers()->sync($ids);

        return new JsonResponse(status: Response::HTTP_OK);
    }

    public function checkValidity(array $validatedInputs, Question $question): void
    {
        throw_if(
            count($validatedInputs['answers']) > $question->no_of_answers,
            ValidationException::withMessages(['answers' => "Exceeds allowed no of answers ({$question->no_of_answers})"])
        );

        $correctAnswersCount = collect($validatedInputs['answers'])
            ->pluck('correct')
            ->filter()
            ->count();

        throw_if(
            $correctAnswersCount < 1,
            ValidationException::withMessages(['answers' => 'At least one correct answer required'])
        );

        throw_if(
            $correctAnswersCount > 1 && $question->is_answers_type_single,
            ValidationException::withMessages(['answers' => 'Single answers type question shouln \'t have more than one correct answers'])
        );
    }

    public function prepareInputs(array $validatedInputs): array
    {
        $answers = $validatedInputs['answers'];

        $inputs = [];

        foreach ($answers as $answer) {
            $inputs[Hashids::decode($answer['id'])[0]] = ['correct_answer' => $answer['correct']];
        }

        return $inputs;
    }
}
