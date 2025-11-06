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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class IQPatternsData
{
    use HasHashids;

    /**
     * General knowledge questions.
     *
     * @var array<int, array{question: string, difficulty: Difficulty, answers: string[], correctAnswer: int[]}>
     */
    public static $questions = [
        [
            'question' => 'The sequence of figures shows a pattern. If the pattern repeats, how many small circles will figure 4 have?',
            'images' => ['finding_patterns_in_shapes5.jpg'],
            'difficulty' => Difficulty::EASY,
            'answers' => ['7 small circles', '5 small circles', '9 small circles', '6 small circles'],
            'correctAnswer' => [1],
        ],
        [
            'question' => 'The sequence of figures shows a pattern. If the pattern repeats, how many small boxes will figure 3 have?',
            'images' => ['finding_patterns_in_shapes8.jpg'],
            'difficulty' => Difficulty::EASY,
            'answers' => ['15 small boxes', '16 small boxes', '17 small boxes', '18 small boxes'],
            'correctAnswer' => [2],
        ],
        [
            'question' => 'The sequence of figures shows a pattern. If the pattern repeats, how many smoke signs will Stage 5 have?',
            'images' => ['finding_patterns_in_shapes9.jpg'],
            'difficulty' => Difficulty::EASY,
            'answers' => ['3 smoke signs', '4 smoke signs', '5 smoke signs', '6 smoke signs'],
            'correctAnswer' => [3],
        ],
        [
            'question' => 'Which of the following shapes does not belong?',
            'images' => ['M24XqFU2C.webp'],
            'difficulty' => Difficulty::EASY,
            'answers' => ['1', '2', '3', '4'],
            'answerImages' => ['1' => '5R_KkCWsS.webp', '2' => 'c3_o0RSuz.webp', '3' => 'JVxpJVFbg.webp', '4' => 'vR0Nrt7Ny.webp'],
            'correctAnswer' => [1],
        ],
        [
            'question' => 'What is the missing number in this puzzle?',
            'images' => ['OO9PSa0Rs.webp'],
            'difficulty' => Difficulty::EASY,
            'answers' => ['7', '5', '22', '3'],
            'correctAnswer' => [2],
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
            ['name' => 'IQ Patterns',  'created_at' => now(), 'updated_at' => now()],
        ]);

        $cat = Category::query()
            ->where('name', 'IQ Patterns')
            ->first();

        $lastQuestionId = Question::query()->orderBy('id', 'desc')->first()?->id ?? 0;

        $imageIndex = 0;

        foreach (self::$questions as $index => $question) {

            if (Arr::get($question, 'images', 0)) {
                foreach ($question['images'] as $image) {
                    Media::query()
                        ->create([
                            'model_type' => (new Question)->getMorphClass(),
                            'model_id' => $lastQuestionId + $index + 1,
                            'collection_name' => 'default',
                            'name' => $image,
                            'file_name' => $image,
                            'disk' => 'public',
                            'size' => 100,
                            'manipulations' => [],
                            'custom_properties' => [],
                            'mime_type' => 'image/jpeg',
                            'generated_conversions' => [],
                            'responsive_images' => [],
                        ]);
                }
            }

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

                if (Arr::get($question, 'answerImages', 0) && array_key_exists((string) ($aIndex + 1), $question['answerImages'])) {
                    Media::query()
                        ->create([
                            'model_type' => (new Answer)->getMorphClass(),
                            'model_id' => $lastAnsId,
                            'collection_name' => 'default',
                            'name' => $question['answerImages'][(string) ($aIndex + 1)],
                            'file_name' => $question['answerImages'][(string) ($aIndex + 1)],
                            'disk' => 'public',
                            'size' => 100,
                            'manipulations' => [],
                            'custom_properties' => [],
                            'mime_type' => 'image/jpeg',
                            'generated_conversions' => [],
                            'responsive_images' => [],
                        ]);
                }
            }
        }

        DB::table('categorizables')->insert($categorizables);
        DB::table('questions')->insert($questions);
        DB::table('answers')->insert($answers);
        DB::table('question_answer')->insert($questionAnswers);

        self::createQuestionnaire('IQ Patterns Test 01');
    }

    private static function createQuestionnaire(string $name): void
    {
        $totalQuestionsCount = count(self::$questions);
        $easyQuestionsCount = count(array_filter(self::$questions, fn ($q) => $q['difficulty'] === Difficulty::EASY));
        $mediumQuestionsCount = count(array_filter(self::$questions, fn ($q) => $q['difficulty'] === Difficulty::MEDIUM));
        $hardQuestionsCount = count(array_filter(self::$questions, fn ($q) => $q['difficulty'] === Difficulty::HARD));

        $questionnaire = Questionnaire::create([
            'name' => $name,
            'difficulty' => Difficulty::EASY,
            'single_answers_type' => true,
            'no_of_questions' => $totalQuestionsCount,
            'no_of_easy_questions' => $easyQuestionsCount,
            'no_of_medium_questions' => $mediumQuestionsCount,
            'no_of_hard_questions' => $hardQuestionsCount,
            'allocated_time' => 5, // 30 minutes
        ]);

        $categories = Category::query()->whereIn('name', ['IQ Patterns'])->pluck('id');

        $questionnaire->categories()->attach($categories);

        foreach (self::$questions as $questionData) {
            $question = Question::where('text', $questionData['question'])->first();
            if ($question) {
                $questionnaire->questions()->attach($question->id, ['marks' => 1]);
            }
        }
    }
}
