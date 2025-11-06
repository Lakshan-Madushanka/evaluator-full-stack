<?php

declare(strict_types=1);

namespace Database\Data;

use App\Enums\Difficulty;
use App\Models\Answer;
use App\Models\Category;
use App\Models\Concerns\HasHashids;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Services\PrettyIdGenerator;
use Illuminate\Support\Facades\DB;

class IQData
{
    use HasHashids;

    /**
     * General knowledge questions.
     *
     * @var array<int, array{question: string, difficulty: Difficulty, answers: string[], correctAnswer: int[]}>
     */
    public static $questions = [
        [
            'question' => 'What is the capital city of France?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['Paris', 'Lyon', 'Marseille', 'Nice'],
            'correctAnswer' => [1],
        ],
        [
            'question' => 'Which number completes the sequence: 2, 4, 8, 16, ?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['18', '24', '32', '34'],
            'correctAnswer' => [3],
        ],
        [
            'question' => 'If all Bloops are Razzies and all Razzies are Lupps, are all Bloops Lupps?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['Yes', 'No'],
            'correctAnswer' => [1],
        ],
        [
            'question' => 'Which word is the odd one out?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['Dog', 'Cat', 'Fish', 'Carrot'],
            'correctAnswer' => [4],
        ],
        [
            'question' => 'Find the next number in the Fibonacci-like sequence: 1, 1, 2, 3, 5, ?',
            'difficulty' => Difficulty::MEDIUM,
            'answers' => ['8', '6', '7', '10'],
            'correctAnswer' => [1],
        ],
        [
            'question' => 'Select the prime numbers from the list.',
            'difficulty' => Difficulty::MEDIUM,
            'answers' => ['4', '5', '9', '11'],
            'correctAnswer' => [2, 4],
        ],
        [
            'question' => 'If 5 workers build 5 walls in 5 days, how many days will 10 workers take to build 10 similar walls?',
            'difficulty' => Difficulty::MEDIUM,
            'answers' => ['2', '5', '10', '25'],
            'correctAnswer' => [2],
        ],
        [
            'question' => 'What comes next in the letter series: Z, X, V, T, ?',
            'difficulty' => Difficulty::MEDIUM,
            'answers' => ['R', 'P', 'S', 'Q'],
            'correctAnswer' => [1],
        ],
        [
            'question' => 'Complete the sequence of square numbers: 1, 4, 9, 16, ?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['20', '24', '25', '30'],
            'correctAnswer' => [3],
        ],
        [
            'question' => 'Analogy: Book is to Reading as Fork is to ?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['Eating', 'Writing', 'Stirring', 'Jumping'],
            'correctAnswer' => [1],
        ],
        [
            'question' => 'Which number replaces the question mark: 3, 9, 27, ?, 243',
            'difficulty' => Difficulty::MEDIUM,
            'answers' => ['54', '72', '81', '90'],
            'correctAnswer' => [3],
        ],
        [
            'question' => 'If 7 people take 7 minutes to make 7 widgets, how many minutes does 1 person take to make 1 widget?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['1', '7', '49', '14'],
            'correctAnswer' => [2],
        ],
        [
            'question' => 'Two trains are 60 miles apart and move toward each other at 20 mph and 40 mph. After how many hours will they meet?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['0.5', '1', '1.5', '2'],
            'correctAnswer' => [2],
        ],
        [
            'question' => 'A small gear with 10 teeth meshes with a larger gear with 15 teeth. If the small gear makes 3 full revolutions, how many revolutions does the larger gear make?',
            'difficulty' => Difficulty::HARD,
            'answers' => ['1', '2', '3', '4'],
            'correctAnswer' => [2],
        ],
        [
            'question' => 'Which number does not belong: 2, 3, 5, 9, 11?',
            'difficulty' => Difficulty::HARD,
            'answers' => ['2', '3', '5', '9', '11'],
            'correctAnswer' => [4],
        ],
    ];

    public static function seedQuestions(): void
    {
        $questions = [];
        $answers = [];
        $questionAnswers = [];
        $categorizables = [];

        $lastAnsId = Answer::query()->orderBy('id', 'desc')->first()?->id ?? 0;

        DB::table('categories')->insert([
            ['name' => 'IQ Test 01',  'created_at' => now(), 'updated_at' => now()],
        ]);

        $cat = Category::query()
            ->where('name', 'IQ Test 01')
            ->first();

        $lastQuestionId = Question::query()->orderBy('id', 'desc')->first()?->id ?? 0;

        foreach (self::$questions as $index => $question) {
            $q = [];
            $q['is_answers_type_single'] = count($question['correctAnswer']) === 1;
            $q['text'] = $question['question'];
            $q['difficulty'] = $question['difficulty']->value;
            $q['no_of_answers'] = count($question['answers']);
            $q['pretty_id'] = PrettyIdGenerator::generate('questions', 'quest_'.$index, 13);
            $q['created_at'] = now();
            $q['updated_at'] = now();

            $questions[] = $q;

            $category = [];
            $category['category_id'] = $cat->getKey();
            $category['categorizable_id'] = $lastQuestionId + $index + 1;
            $category['categorizable_type'] = (new Question)->getMorphClass();
            $category['created_at'] = now();
            $category['updated_at'] = now();

            $categorizables[] = $category;

            foreach ($question['answers'] as $aIndex => $answer) {
                $qa = [];

                $a = [];
                $a['pretty_id'] = PrettyIdGenerator::generate('answers', 'ans_'.$lastAnsId, 13);
                $a['text'] = $answer;
                $a['created_at'] = now();
                $a['updated_at'] = now();

                $answers[] = $a;

                $qa['question_id'] = $lastQuestionId + $index + 1;
                $qa['answer_id'] = ++$lastAnsId;
                $qa['correct_answer'] = in_array($aIndex + 1, $question['correctAnswer'], true);
                $qa['created_at'] = now();
                $qa['updated_at'] = now();

                $questionAnswers[] = $qa;
            }
        }

        DB::table('categorizables')->insert($categorizables);
        DB::table('questions')->insert($questions);
        DB::table('answers')->insert($answers);
        DB::table('question_answer')->insert($questionAnswers);

        self::createQuestionnaire('IQ Test 01');
    }

    private static function createQuestionnaire(string $name): void
    {
        $totalQuestionsCount = count(self::$questions);
        $easyQuestionsCount = count(array_filter(self::$questions, fn ($q) => $q['difficulty'] === Difficulty::EASY));
        $mediumQuestionsCount = count(array_filter(self::$questions, fn ($q) => $q['difficulty'] === Difficulty::MEDIUM));
        $hardQuestionsCount = count(array_filter(self::$questions, fn ($q) => $q['difficulty'] === Difficulty::HARD));

        $questionnaire = Questionnaire::create([
            'name' => $name,
            'difficulty' => Difficulty::HARD,
            'single_answers_type' => false,
            'no_of_questions' => $totalQuestionsCount,
            'no_of_easy_questions' => $easyQuestionsCount,
            'no_of_medium_questions' => $mediumQuestionsCount,
            'no_of_hard_questions' => $hardQuestionsCount,
            'allocated_time' => 30, // 30 minutes
        ]);

        $categories = Category::query()->whereIn('name', ['IQ Test 01'])->pluck('id');

        $questionnaire->categories()->attach($categories);

        foreach (self::$questions as $questionData) {
            $question = Question::where('text', $questionData['question'])->first();
            if ($question) {
                $questionnaire->questions()->attach($question->id, ['marks' => 1]);
            }
        }
    }
}
