<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Difficulty;
use App\Models\Question;
use App\Models\Questionnaire;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Questionnaire::query()
            ->with('categories:id')
            ->get()
            ->each(function (Questionnaire $questionnaire) {
                $noOfEasyQuestions = $questionnaire->no_of_easy_questions;
                $noOfMediumQuestions = $questionnaire->no_of_medium_questions;
                $noOfHardQuestions = $questionnaire->no_of_hard_questions;

                $easyQuestions = $questionnaire->questions()->createMany($this->makeQuestions(Difficulty::EASY,
                    $noOfEasyQuestions, $questionnaire));
                $mediumQuestions = $questionnaire->questions()->createMany($this->makeQuestions(Difficulty::MEDIUM,
                    $noOfMediumQuestions, $questionnaire));
                $hardQuestions = $questionnaire->questions()->createMany($this->makeQuestions(Difficulty::HARD,
                    $noOfHardQuestions, $questionnaire));

                $this->assignCategories($easyQuestions, $questionnaire->categories);
                $this->assignCategories($mediumQuestions, $questionnaire->categories);
                $this->assignCategories($hardQuestions, $questionnaire->categories);
            });
    }

    public function makeQuestions(Difficulty $type, int $noOfQuestions, Questionnaire $questionnaire): array
    {
        if ($noOfQuestions < 1) {
            return [];
        }

        return Question::factory()
            ->state(function (array $attributes) use ($questionnaire) {
                if ($questionnaire->single_answers_type) {
                    return ['is_answers_type_single' => true];
                }

                return ['is_answers_type_single' => false];
            })
            ->count($noOfQuestions)
            ->make(['difficulty' => $type])
            ->toArray();
    }

    public function assignCategories(array $questions, Collection $categories)
    {
        foreach ($questions as $question) {
            /** @var Question $question */
            $question->categories()->sync($categories->pluck('id'));
        }
    }
}
