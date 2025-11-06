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

class GNData
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
            'question' => 'Which planet is known as the Red Planet?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['Venus', 'Mars', 'Jupiter', 'Saturn'],
            'correctAnswer' => [2],
        ],
        [
            'question' => 'Which of these are programming languages?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['Ruby', 'Eiffel Tower', 'Python', 'Mount Everest'],
            'correctAnswer' => [1, 3],
        ],
        [
            'question' => 'Which country hosted the 2016 Summer Olympics?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['China', 'Brazil', 'United Kingdom', 'Russia'],
            'correctAnswer' => [2],
        ],
        [
            'question' => 'Who wrote the play "Romeo and Juliet"?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['William Shakespeare', 'Charles Dickens', 'Jane Austen', 'Mark Twain'],
            'correctAnswer' => [1],
        ],
        [
            'question' => 'What is the chemical symbol for gold?',
            'difficulty' => Difficulty::MEDIUM,
            'answers' => ['Au', 'Ag', 'Gd', 'Go'],
            'correctAnswer' => [1],
        ],
        [
            'question' => 'Which organ in the human body is primarily responsible for detoxification and metabolism?',
            'difficulty' => Difficulty::MEDIUM,
            'answers' => ['Heart', 'Liver', 'Kidney', 'Lungs'],
            'correctAnswer' => [2],
        ],
        [
            'question' => 'Which of the following countries are members of the United Kingdom?',
            'difficulty' => Difficulty::MEDIUM,
            'answers' => ['England', 'Scotland', 'Ireland', 'Wales'],
            'correctAnswer' => [1, 2, 4],
        ],
        [
            'question' => 'What is the largest ocean on Earth?',
            'difficulty' => Difficulty::MEDIUM,
            'answers' => ['Atlantic Ocean', 'Indian Ocean', 'Pacific Ocean', 'Arctic Ocean'],
            'correctAnswer' => [3],
        ],
        [
            'question' => 'Which scientist developed the theory of general relativity?',
            'difficulty' => Difficulty::MEDIUM,
            'answers' => ['Isaac Newton', 'Albert Einstein', 'Niels Bohr', 'Galileo Galilei'],
            'correctAnswer' => [2],
        ],
        [
            'question' => 'Which of these countries use the Euro as their official currency?',
            'difficulty' => Difficulty::MEDIUM,
            'answers' => ['Germany', 'Sweden', 'Spain', 'Poland'],
            'correctAnswer' => [1, 3],
        ],
        [
            'question' => 'Which year did the Berlin Wall fall?',
            'difficulty' => Difficulty::HARD,
            'answers' => ['1987', '1989', '1991', '1993'],
            'correctAnswer' => [2],
        ],
        [
            'question' => 'Which element has atomic number 6?',
            'difficulty' => Difficulty::HARD,
            'answers' => ['Carbon', 'Nitrogen', 'Oxygen', 'Boron'],
            'correctAnswer' => [1],
        ],
        [
            'question' => 'In computing, what does "HTTP" stand for?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['HyperText Transfer Protocol', 'Hyperlink Text Transfer Process', 'Hyper Transmission Transfer Protocol', 'HyperText Transmission Procedure'],
            'correctAnswer' => [1],
        ],
        [
            'question' => 'Which composer wrote the "Fifth Symphony" commonly associated with the motif "short-short-short-long"?',
            'difficulty' => Difficulty::MEDIUM,
            'answers' => ['Ludwig van Beethoven', 'Wolfgang Amadeus Mozart', 'Johann Sebastian Bach', 'Pyotr Ilyich Tchaikovsky'],
            'correctAnswer' => [1],
        ],
        [
            'question' => 'Which country has the largest land area in the world?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['Canada', 'United States', 'Russia', 'China'],
            'correctAnswer' => [3],
        ],
        [
            'question' => 'What is the powerhouse of the cell?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['Nucleus', 'Mitochondrion', 'Ribosome', 'Golgi apparatus'],
            'correctAnswer' => [2],
        ],
        [
            'question' => 'Which of the following are types of rock?',
            'difficulty' => Difficulty::MEDIUM,
            'answers' => ['Igneous', 'Sedimentary', 'Metamorphic', 'Polymeric'],
            'correctAnswer' => [1, 2, 3],
        ],
        [
            'question' => 'Who was the first person to reach the South Pole?',
            'difficulty' => Difficulty::HARD,
            'answers' => ['Roald Amundsen', 'Robert Scott', 'Ernest Shackleton', 'Ferdinand Magellan'],
            'correctAnswer' => [1],
        ],
        [
            'question' => 'Which language family does Spanish belong to?',
            'difficulty' => Difficulty::MEDIUM,
            'answers' => ['Germanic', 'Romance', 'Slavic', 'Uralic'],
            'correctAnswer' => [2],
        ],
        [
            'question' => 'What device measures atmospheric pressure?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['Thermometer', 'Barometer', 'Hygrometer', 'Anemometer'],
            'correctAnswer' => [2],
        ],
        [
            'question' => 'Which of these mathematical constants is irrational?',
            'difficulty' => Difficulty::MEDIUM,
            'answers' => ['22/7', 'π (pi)', '4', '1/2'],
            'correctAnswer' => [2],
        ],
        [
            'question' => 'Which gas makes up the majority of Earth’s atmosphere?',
            'difficulty' => Difficulty::EASY,
            'answers' => ['Oxygen', 'Carbon Dioxide', 'Nitrogen', 'Argon'],
            'correctAnswer' => [3],
        ],
        [
            'question' => 'Which ancient civilization built Machu Picchu?',
            'difficulty' => Difficulty::HARD,
            'answers' => ['Aztec', 'Inca', 'Maya', 'Olmec'],
            'correctAnswer' => [2],
        ],
        [
            'question' => 'Which of the following are SI base units?',
            'difficulty' => Difficulty::HARD,
            'answers' => ['Meter', 'Second', 'Liter', 'Mole'],
            'correctAnswer' => [1, 2, 4],
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
            ['name' => 'General Knowledge',  'created_at' => now(), 'updated_at' => now()],
        ]);

        $cat = Category::query()
            ->where('name', 'General Knowledge')
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

        self::createQuestionnaire('General Knowledge Test 01');
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

        $categories = Category::query()->whereIn('name', ['General Knowledge'])->pluck('id');

        $questionnaire->categories()->attach($categories);

        foreach (self::$questions as $questionData) {
            $question = Question::where('text', $questionData['question'])->first();
            if ($question) {
                $questionnaire->questions()->attach($question->id, ['marks' => 1]);
            }
        }
    }
}
