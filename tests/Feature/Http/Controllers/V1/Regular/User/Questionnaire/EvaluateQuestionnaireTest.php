<?php

use App\Enums\Difficulty;
use App\Models\Answer;
use App\Models\Evaluation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\User;
use App\Models\UserQuestionnaire;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\postJson;

it('throws exception if submitted time exceeded allocated time', function () {
    $userQuestionnaire = UserQuestionnaire::query()
        ->where('attempts', 0)
        ->firstOrFail();
    $userQuestionnaire->expires_at = now()->addMinutes(30);
    $userQuestionnaire?->save();

    $questionnaire = Questionnaire::query()->findOrFail($userQuestionnaire?->questionnaire_id);

    $userQuestionnaire->started_at = now()->subMinutes($questionnaire->allocated_time + 10);
    $userQuestionnaire->save();

    $response = postJson(route('api.v1.users.questionnaires.evaluate',
        ['code' => $userQuestionnaire?->code]));

    $response->assertStatus(400);
})->group('regular/users/questionnaires/evaluate');

it('it can evaluate user questionnaire', function () {
    [
        'questionnaire' => $questionnaire,
        'userQuestionnaire' => $userQuestionnaire,
        'selectedAnswers' => $selectedAnswers,
        'totalMarksPercentage' => $totalMarksPercentage,
        'noOfAnsweredQuestions' => $noOfAnsweredQuestions,
        'noOfCorrectAnswers' => $noOfCorrectAnswers,
        'total_points_earned' => $totalPointsEarned,
        'total_points_allocated' => $totalPointsAllocated,
    ]
        = setQuestionnaireToEvaluate();

    $response = postJson(
        route('api.v1.users.questionnaires.evaluate', ['code' => $userQuestionnaire?->code]),
        ['answers' => $selectedAnswers]
    );

    $response->assertCreated();

    $userQuestionnaire->refresh();
    $evaluation = Evaluation::where('user_questionnaire_id', $userQuestionnaire->id)->firstOrFail();

    expect($userQuestionnaire->answers)->not()->toBeNull();
    expect($evaluation->time_taken)->toBeLessThan(2);

    $response->assertJson(fn (AssertableJson $json) => $json->where('data.type', 'evaluations')
        ->where('data.attributes.marks_percentage', $totalMarksPercentage)
        ->where('data.attributes.no_of_answered_questions', $noOfAnsweredQuestions)
        ->where('data.attributes.no_of_correct_answers', $noOfCorrectAnswers)
        ->where('data.attributes.total_points_earned', $totalPointsEarned)
        ->where('data.attributes.total_points_allocated', $totalPointsAllocated)
        ->etc()
    );

    // Resend the request to test t won't re-evaluate instead  it should return results.
    $response2 = postJson(
        route('api.v1.users.questionnaires.evaluate', ['code' => $userQuestionnaire?->code]),
        ['answers' => $selectedAnswers]
    );

    $evaluation2 = $evaluation->fresh();

    $response2->assertOk();
    $response2->assertJsonPath('data.attributes.marks_percentage', $totalMarksPercentage);

    expect($evaluation->updated_at->getTimestamp())->toBe($evaluation2?->updated_at->getTimestamp());
})->group('regular/users/questionnaires/evaluate');

function setQuestionnaireToEvaluate(): array
{
    $questionnaire = Questionnaire::create([
        'name' => \Illuminate\Support\Str::random(),
        'difficulty' => Difficulty::MEDIUM,
        'single_answers_type' => false,
        'allocated_time' => 15,
        'no_of_questions' => 10,
        'no_of_easy_questions' => 5,
        'no_of_medium_questions' => 3,
        'no_of_hard_questions' => 2,
    ]);

    $easyQuestions = Question::factory()->count(5)->create(['difficulty' => Difficulty::EASY]);
    $mediumQuestions = Question::factory()->count(3)->create(['difficulty' => Difficulty::MEDIUM]);
    $hardQuestions = Question::factory()->count(2)->create([
        'is_answers_type_single' => true,
        'difficulty' => Difficulty::HARD,
    ]);

    $selectedAnswers = [];

    $easyQuestions->merge($mediumQuestions)->each(function (Question $question) use (
        &$selectedAnswers
    ) {
        $answersIds = Answer::factory()->count(4)->create()->pluck('id');

        $data = [];

        $answersIds->each(function (int $id, int $index) use (&$data) {
            /*$correctAnswer = $index === 0 ? true : array_rand([true, false]);
            $data[$id] = ['correct_answer' => $correctAnswer];*/

            if ($index === 0) {
                $data[$id] = ['correct_answer' => true];
                $userAnswers[] = $id;
            } else {
                $data[$id] = ['correct_answer' => false];
                $userAnswers[] = $id;
            }
        });
        $question->answers()->attach($data);

        // User receive 0 marks for answers
        $selectedAnswers[\Vinkla\Hashids\Facades\Hashids::encode($question->id)] = \App\Helpers::getHashIdsFromModelIds($answersIds->all());
    });

    $hardQuestions->each(function (Question $question) use (&$selectedAnswers) {
        $answersIds = Answer::factory()->count(2)->create()->pluck('id');

        $data = [];

        $answersIds->each(function (int $id, int $index) use (&$data, $question, &$selectedAnswers) {
            if ($index === 0) {
                $data[$id] = ['correct_answer' => true];
                // User receive 1.625 mark for each hard questions (total 3.25)
                $selectedAnswers[\Vinkla\Hashids\Facades\Hashids::encode($question->id)] = [\Vinkla\Hashids\Facades\Hashids::encode([$id])];
            } else {
                $data[$id] = ['correct_answer' => false];
            }
        });
        $question->answers()->attach($data);
    });

    $easyQuestionsIds = $easyQuestions->pluck('id');
    $mediumQuestionsIds = $mediumQuestions->pluck('id');
    $hardQuestionsIds = $hardQuestions->pluck('id');

    $questionnaire->questions()->attach($easyQuestionsIds, ['marks' => 0.75]); // total = 3.75
    $questionnaire->questions()->attach($mediumQuestionsIds, ['marks' => 1]); // total = 3
    $questionnaire->questions()->attach($hardQuestionsIds, ['marks' => 1.625]); // total = 3.25

    $userQuestionnaire = UserQuestionnaire::create([
        'questionnaire_id' => $questionnaire->id,
        'user_id' => User::first()->id,
        'expires_at' => now()->addMinutes($questionnaire->allocated_time * 2),
        'started_at' => now(),
        'code' => \Illuminate\Support\Str::uuid(),
    ]);

    return [
        'questionnaire' => $questionnaire,
        'userQuestionnaire' => $userQuestionnaire,
        'selectedAnswers' => $selectedAnswers,
        'totalMarksPercentage' => 32.5,
        'noOfAnsweredQuestions' => 10,
        'noOfCorrectAnswers' => 2,
        'total_points_earned' => 3.3,
        'total_points_allocated' => 10,
    ];
}
