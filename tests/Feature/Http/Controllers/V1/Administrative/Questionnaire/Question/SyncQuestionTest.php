<?php

use App\Enums\Difficulty;
use App\Enums\Role;
use App\Models\Answer;
use App\Models\Category;
use App\Models\Question;
use App\Models\Questionnaire;
use Illuminate\Support\Collection;
use Laravel\Sanctum\Sanctum;
use Tests\Repositories\QuestionRepository;
use Tests\Repositories\UserRepository;

use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->questionnaire = Questionnaire::factory()->create([
        'single_answers_type' => false,
        'no_of_questions' => 6,
        'no_of_easy_questions' => 2,
        'no_of_medium_questions' => 2,
        'no_of_hard_questions' => 2,
    ]);

    $category = Category::query()->first();

    $this->questionnaire->categories()->attach($category?->id);

    $answersIds = Answer::query()->limit(2)->pluck('id');

    $easyQuestions = Question::factory()->count(4)->create([
        'is_answers_type_single' => false,
        'difficulty' => Difficulty::EASY,
        'no_of_answers' => 2,
    ])
        ->each(fn (Question $question) => $question->answers()->sync($answersIds));

    $mediumQuestions = Question::factory()->count(4)->create([
        'is_answers_type_single' => false,
        'difficulty' => Difficulty::MEDIUM,
        'no_of_answers' => 2,
    ])
        ->each(fn (Question $question) => $question->answers()->sync($answersIds));

    $hardQuestions = Question::factory()->count(4)->create([
        'is_answers_type_single' => false,
        'difficulty' => Difficulty::HARD,
        'no_of_answers' => 2,
    ])
        ->each(fn (Question $question) => $question->answers()->sync($answersIds));

    $easyQuestionsIds = $easyQuestions->pluck('id');
    $mediumQuestionsIds = $mediumQuestions->pluck('id');
    $hardQuestionsIds = $hardQuestions->pluck('id');

    $ids = $easyQuestionsIds->merge([$mediumQuestionsIds, $hardQuestionsIds])->flatten()->all();

    $category?->questions()->sync($ids);
});

it('return 401 unauthorized response for non-login users', function () {
    $response = postJson(
        route('api.v1.administrative.questionnaires.questions.sync', ['questionnaire' => 'abcd']),
        ['question' => []]
    );
    $response->assertUnauthorized();
})->group('api/v1/administrative/questionnaire/question/sync');

it('throws validation exception when try to sync no of question greater than allowed no of question', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $payload = QuestionRepository::pluckCompletedQuestionsHashIds($this->questionnaire->no_of_questions + 1,
        $this->questionnaire);

    $response = postJson(route(
        'api.v1.administrative.questionnaires.questions.sync',
        ['questionnaire' => $this->questionnaire->hash_id]
    ),
        prepareDataforSyncQuestiontoQuestionnaire($payload)
    );

    $response->assertUnprocessable();
    $response->assertInvalid(['questions']);
})->group('api/v1/administrative/questionnaire/question/sync');

it('throws validation exception when try to sync no of easy question greater than allowed no of easy question',
    function () {
        Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

        $payload = QuestionRepository::pluckCompletedQuestionsHashIdsByDifficulty(
            $this->questionnaire->no_of_easy_questions + 1,
            Difficulty::EASY,
            $this->questionnaire
        );

        $response = postJson(
            route('api.v1.administrative.questionnaires.questions.sync',
                ['questionnaire' => $this->questionnaire->hash_id]),
            prepareDataforSyncQuestiontoQuestionnaire($payload)
        );

        $response->assertUnprocessable();
        $response->assertInvalid(['questions']);
    })->group('api/v1/administrative/questionnaire/question/sync');

it('throws validation exception when try to sync no of medium question greater than allowed no of medium question',
    function () {
        Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

        $payload = QuestionRepository::pluckCompletedQuestionsHashIdsByDifficulty(
            $this->questionnaire->no_of_medium_questions + 1,
            Difficulty::MEDIUM,
            $this->questionnaire
        );

        $response = postJson(
            route(
                'api.v1.administrative.questionnaires.questions.sync',
                ['questionnaire' => $this->questionnaire->hash_id]
            ),
            prepareDataforSyncQuestiontoQuestionnaire($payload)
        );

        $response->assertUnprocessable();
        $response->assertInvalid(['questions']);
    })->group('api/v1/administrative/questionnaire/question/sync');

it('throws validation exception when try to sync no of hard question greater than allowed no of hard question',
    function () {
        Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

        $payload = QuestionRepository::pluckCompletedQuestionsHashIdsByDifficulty(
            $this->questionnaire->no_of_hard_questions + 1,
            Difficulty::HARD,
            $this->questionnaire
        );

        $response = postJson(
            route('api.v1.administrative.questionnaires.questions.sync',
                ['questionnaire' => $this->questionnaire->hash_id]),
            prepareDataforSyncQuestiontoQuestionnaire($payload)
        );

        $response->assertUnprocessable();
        $response->assertInvalid(['questions']);
    })->group('api/v1/administrative/questionnaire/question/sync');

it('allows administrative users to sync questions',
    function () {
        Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

        $easyQuestions = QuestionRepository::pluckCompletedQuestionsHashIdsByDifficulty(
            $this->questionnaire->no_of_easy_questions,
            Difficulty::EASY,
            $this->questionnaire
        );
        $mediumQuestions = QuestionRepository::pluckCompletedQuestionsHashIdsByDifficulty(
            $this->questionnaire->no_of_medium_questions,
            Difficulty::MEDIUM,
            $this->questionnaire
        );
        $hardQuestions = QuestionRepository::pluckCompletedQuestionsHashIdsByDifficulty(
            $this->questionnaire->no_of_hard_questions,
            Difficulty::HARD,
            $this->questionnaire
        );

        $payload = $easyQuestions->concat([$mediumQuestions, $hardQuestions])->flatten();

        $response = postJson(
            route('api.v1.administrative.questionnaires.questions.sync',
                ['questionnaire' => $this->questionnaire->hash_id]),
            prepareDataforSyncQuestiontoQuestionnaire($payload)
        );

        $response->assertOk();

        $questionsIds = $this->questionnaire->questions->pluck('id')
            ->transform(fn ($id) => \Vinkla\Hashids\Facades\Hashids::encode($id));

        expect($payload->diff($questionsIds))->toEqual(collect([]));
    })->group('api/v1/administrative/questionnaire/question/sync');

it('allows administrative users to remove all questions of a questionnaire',
    function () {
        Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

        $response = postJson(
            route('api.v1.administrative.questionnaires.questions.sync',
                ['questionnaire' => $this->questionnaire->hash_id]),
            ['questions' => []]
        );

        $response->assertOk();

        $questionsIds = $this->questionnaire->questions->pluck('id');

        expect($questionsIds->count())->toEqual(0);
    })->group('api/v1/administrative/questionnaire/question/sync');

it('cannot attach question which belongs to different category than questionnaire',
    function () {
        Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

        $questionnaireCategoryIds = $this->questionnaire->categories()->pluck('categories.id');

        $question = Question::factory()
            ->create();

        $answers = Answer::limit($question->no_of_answers)->pluck('id');

        $question->answers()->sync($answers);

        $response = postJson(
            route('api.v1.administrative.questionnaires.questions.sync',
                ['questionnaire' => $this->questionnaire->hash_id]),
            [
                'questions' => [
                    [
                        'id' => \Vinkla\Hashids\Facades\Hashids::encode($question->id),
                        'marks' => 1,
                    ],
                ],
            ]
        );

        $response->assertOk();

        $questionsIds = $this->questionnaire->questions->pluck('id');

        expect($questionsIds->count())->toEqual(0);
    })->group('api/v1/administrative/questionnaire/question/sync');

it('cannot sync same question twice',
    function () {
        Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

        $payload = QuestionRepository::pluckCompletedQuestionsHashIdsByDifficulty(
            $this->questionnaire->no_of_hard_questions - 1,
            Difficulty::HARD,
            $this->questionnaire
        );

        $payload->push($payload[0]);

        $response = postJson(
            route('api.v1.administrative.questionnaires.questions.sync',
                ['questionnaire' => $this->questionnaire->hash_id]),
            prepareDataforSyncQuestiontoQuestionnaire($payload)
        );

        $attachedQuestions = $this->questionnaire->questions;

        expect($attachedQuestions->count())->toBe(1);
    })->group('api/v1/administrative/questionnaire/question/sync');

it('cannot sync a multiple answer type question when questionnaire is
single answer type', function () {
    Sanctum::actingAs(UserRepository::getRandomUser(Role::ADMIN));

    $questionnaire = Questionnaire::factory()->create([
        'single_answers_type' => true,
        'no_of_questions' => 6,
        'no_of_easy_questions' => 2,
        'no_of_medium_questions' => 2,
        'no_of_hard_questions' => 2,
    ]);

    $category = Category::query()->first();

    $questionnaire->categories()->attach($category?->id);

    $answersIds = Answer::query()->limit(2)->pluck('id');

    $question = Question::factory()->create([
        'is_answers_type_single' => false,
        'difficulty' => Difficulty::MEDIUM,
        'no_of_answers' => 2,
    ]);

    $question->answers()->sync($answersIds);
    $question->categories()->attach($category?->id);

    $response = postJson(route(
        'api.v1.administrative.questionnaires.questions.sync',
        ['questionnaire' => $questionnaire?->hash_id]),
        [
            'questions' => [
                [
                    'id' => $question?->hash_id,
                    'marks' => 1,
                ],
            ],
        ]
    );

    $attachedQuestions = $questionnaire?->questions;

    $response->assertOk();
    expect($attachedQuestions->count())->toBe(0);
})->group('api/v1/administrative/questionnaire/question/sync');

function prepareDataforSyncQuestiontoQuestionnaire(Collection $ids, int $marks = 1): array
{
    $data = [];

    $ids = $ids->all();

    foreach ($ids as $id) {
        $data[] = ['id' => $id, 'marks' => $marks];
    }

    return ['questions' => $data];
}
