<?php

namespace Database\Data;

use App\Enums\Difficulty;
use App\Models\Category;
use App\Models\Concerns\HasHashids;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Services\PrettyIdGenerator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProgrammingData
{
    use HasHashids;

    public static $questions = [
        [
            'question' => 'What is PHP?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'PHP is an open-source programming language',
                'PHP is used to develop dynamic and interactive websites',
                'PHP is a server-side scripting language',
                'All of the mentioned',
            ],
            'correctAnswer' => 4,
        ],
        [
            'question' => 'Who is the father of PHP?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'Drek Kolkevi',
                'Rasmus Lerdorf',
                'Willam Makepiece',
                'List Barely',
            ],
            'correctAnswer' => 2,
        ],
        [
            'question' => 'What does PHP stand for?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'PHP stands for Preprocessor Home Page',
                'PHP stands for Pretext Hypertext Processor',
                'PHP stands for Hypertext Preprocessor',
                'PHP stands for Personal Hyper Processor',
            ],
            'correctAnswer' => 3,
        ],
        [
            'question' => 'Which of the following is the correct syntax to write a PHP code?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                '<?php ?>',
                '< php >',
                '< ? php ?>',
                '<? ?>',
            ],
            'correctAnswer' => 4,
        ],
        [
            'question' => 'Which of the following is the correct way to add a comment in PHP code?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                '#',
                '//',
                '/* */',
                'All of the mentioned',
            ],
            'correctAnswer' => 4,
        ],
        [
            'question' => 'Which of the following is the default file extension of PHP files?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                '.php',
                '.ph',
                '.xml',
                '.html',
            ],
            'correctAnswer' => 1,
        ],
        [
            'question' => 'How to define a function in PHP?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'functionName(parameters) {function body}',
                'function {function body}',
                'function functionName(parameters) {function body}',
                'data type functionName(parameters) {function body}',
            ],
            'correctAnswer' => 3,
        ],
        [
            'question' => 'Which is the right way of declaring a variable in PHP?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                '$3hello',
                '$_hello',
                '$this',
                '$5_Hello',
            ],
            'correctAnswer' => 2,
        ],
        [
            'question' => 'Which of the following PHP functions can be used for generating unique ids?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'md5()',
                'uniqueid()',
                'mdid()',
                'id()',
            ],
            'correctAnswer' => 2,
        ],
        [
            'question' => 'Which of the following web servers are required to run the PHP script?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'Apache and PHP',
                'IIS',
                'XAMPP',
                'Any of the mentioned',
            ],
            'correctAnswer' => 2,
        ],
        [
            'question' => 'Which symbol is used to prepend variables in PHP?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                '%',
                '$',
                '&',
                '#',
            ],
            'correctAnswer' => 2,
        ],
        [
            'question' => 'Which function is used to output text in PHP?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'echo',
                'printout',
                'display',
                'write',
            ],
            'correctAnswer' => 1,
        ],
        [
            'question' => 'Which of the following is a valid PHP variable name?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                '2name',
                '$name',
                '@name',
                'name$',
            ],
            'correctAnswer' => 2,
        ],
        [
            'question' => 'How do you start a PHP block?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                '<php>',
                '<?php',
                '{php}',
                '[php]',
            ],
            'correctAnswer' => 2,
        ],
        [
            'question' => 'Which function returns the length of a string?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'strlen()',
                'strcount()',
                'length()',
                'countstr()',
            ],
            'correctAnswer' => 1,
        ],
        [
            'question' => 'What is the default file extension for PHP files?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                '.html',
                '.php',
                '.ph',
                '.phtml',
            ],
            'correctAnswer' => 2,
        ],
        [
            'question' => 'Which superglobal contains form data sent via POST?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                '$_GET',
                '$_POST',
                '$_FORM',
                '$_DATA',
            ],
            'correctAnswer' => 2,
        ],
        [
            'question' => 'How do you write a single-line comment in PHP?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                '//',
                '#',
                '<!-- -->',
                'Both A and B',
            ],
            'correctAnswer' => 4,
        ],
        [
            'question' => 'Which function is used to include a file only once?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'include',
                'require',
                'include_once',
                'require_file',
            ],
            'correctAnswer' => 3,
        ],
        [
            'question' => 'Which function is used to check if a file exists?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'file_exists()',
                'exists_file()',
                'check_file()',
                'is_file()',
            ],
            'correctAnswer' => 1,
        ],
        [
            'question' => 'What is the output of echo 2 + "2"?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                '22',
                '4',
                'Error',
                '"2+2"',
            ],
            'correctAnswer' => 2,
        ],
        [
            'question' => 'Which function is used to start a session in PHP?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'session_start()',
                'start_session()',
                'begin_session()',
                'session_begin()',
            ],
            'correctAnswer' => 1,
        ],
        [
            'question' => 'Which operator is used for concatenation in PHP?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                '+',
                '.',
                '&',
                '.',
            ],
            'correctAnswer' => 2,
        ],
        [
            'question' => 'Which function is used to redirect to another page?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'header()',
                'redirect()',
                'goto()',
                'location()',
            ],
            'correctAnswer' => 1,
        ],
        [
            'question' => 'Which function is used to get the current date in PHP?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'now()',
                'date()',
                'getdate()',
                'today()',
            ],
            'correctAnswer' => 2,
        ],
        [
            'question' => 'Which function is used to remove whitespace from both ends of a string?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'trim()',
                'strip()',
                'clean()',
                'remove()',
            ],
            'correctAnswer' => 1,
        ],
        [
            'question' => 'Which function is used to count elements in an array?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'count()',
                'size()',
                'length()',
                'num()',
            ],
            'correctAnswer' => 1,
        ],
        [
            'question' => 'Which function is used to connect to a MySQL database in PHP (mysqli)?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'mysqli_connect()',
                'mysql_connect()',
                'connect_db()',
                'db_connect()',
            ],
            'correctAnswer' => 1,
        ],
        [
            'question' => 'Which keyword is used to define a constant in PHP?',
            'difficulty' => Difficulty::EASY,
            'answers' => [
                'define',
                'const',
                'constant',
                'set',
            ],
            'correctAnswer' => 1,
        ],
    ];

    public static function seedQuestions(): void
    {
        $questions = [];
        $answers = [];
        $questionAnswers = [];
        $categorizables = [];

        $lastAnsId = 0;

        foreach (self::$questions as $index => $question) {
            $q = [];
            $q['is_answers_type_single'] = count($question['answers']) > 2;
            $q['text'] = $question['question'];
            $q['difficulty'] = $question['difficulty']->value;
            $q['no_of_answers'] = count($question['answers']);
            $q['pretty_id'] = PrettyIdGenerator::generate('questions', 'quest_'.$index, 13);
            $q['created_at'] = now();
            $q['updated_at'] = now();

            $questions[] = $q;

            $category = [];
            $category['category_id'] = 1;
            $category['categorizable_id'] = $index + 1;
            $category['categorizable_type'] = (new Question)->getMorphClass();
            $category['created_at'] = now();
            $category['updated_at'] = now();

            $categorizables[] = $category;

            $category2 = $category;
            $category2['category_id'] = 2;

            $categorizables[] = $category2;

            foreach ($question['answers'] as $aIndex => $answer) {
                $qa = [];
                $a = [];
                $a['pretty_id'] = PrettyIdGenerator::generate('answers', 'ans_'.$lastAnsId, 13);
                $a['text'] = $answer;
                $a['created_at'] = now();
                $a['updated_at'] = now();

                $answers[] = $a;

                $qa['question_id'] = $index + 1;
                $qa['answer_id'] = ++$lastAnsId;
                $qa['correct_answer'] = $aIndex + 1 === $question['correctAnswer'];
                $qa['created_at'] = now();
                $qa['updated_at'] = now();

                $questionAnswers[] = $qa;
            }
        }

        DB::table('categories')->insert([
            ['name' => 'PHP',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Programming', 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('categorizables')->insert($categorizables);
        DB::table('questions')->insert($questions);
        DB::table('answers')->insert($answers);
        DB::table('question_answer')->insert($questionAnswers);

        self::createQuestionnaire('PHP Advance', 20);
        self::createQuestionnaire('PHP Medium', 10);
    }

    private static function createQuestionnaire(string $name, int $noOfQuestions): void
    {
        $questionnaire = Questionnaire::create([
            'name' => $name,
            'difficulty' => 1, // Easy
            'single_answers_type' => true,
            'no_of_questions' => $noOfQuestions,
            'no_of_easy_questions' => $noOfQuestions,
            'no_of_medium_questions' => 0,
            'no_of_hard_questions' => 0,
            'allocated_time' => 30, // 30 minutes
        ]);

        $categories = Category::query()->whereIn('name', ['PHP', 'Programming'])->pluck('id');

        $questionnaire->categories()->attach($categories);

        $randomQuestions = Arr::random(ProgrammingData::$questions, $noOfQuestions);

        foreach ($randomQuestions as $index => $questionData) {
            $question = Question::where('text', $questionData['question'])->first();
            if ($question) {
                $questionnaire->questions()->attach($question->id, ['marks' => 1]);
            }
        }
    }
}
