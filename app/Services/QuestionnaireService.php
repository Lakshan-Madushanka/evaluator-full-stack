<?php

namespace App\Services;

use App\Models\Questionnaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class QuestionnaireService
{
    public function checkAvailability(string $questionnaireId): Questionnaire|false
    {
        $decodedQuestionnaireId = $this->decodeId($questionnaireId);

        if (is_null($decodedQuestionnaireId)) {
            return false;
        }

        $questionnaire = Questionnaire::query()
            ->whereId($decodedQuestionnaireId)
            ->withCount('questions')
            ->completed(true)
            ->first();

        if (is_null($questionnaire)) {
            return false;
        }

        return $questionnaire;
    }

    public function decodeId(string $questionnaireId): ?int
    {
        return Hashids::decode($questionnaireId)[0] ?? null;
    }

    public function getAttributes(Questionnaire $questionnaire): array
    {
        return [
            'code' => Str::uuid(),
            'expires_at' => now()->addMinutes($questionnaire->allocated_time * 2),
        ];
    }

    public function ineligibleResponse(): JsonResponse
    {
        return new JsonResponse(data: [
            'eligible' => false,
        ]);
    }
}
