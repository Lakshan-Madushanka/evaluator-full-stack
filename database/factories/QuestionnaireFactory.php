<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Difficulty;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class QuestionnaireFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word().Str::random(8),
            'difficulty' => $this->faker->randomElement(Difficulty::cases()),
            'single_answers_type' => $this->faker->randomElement([true, false]),

            'no_of_questions' => $this->faker->numberBetween(25, 50),
            'no_of_easy_questions' => function (array $attributes) {
                return $this->calcNoOfQuestionsForDifficultyLevel(
                    $attributes['difficulty'],
                    Difficulty::EASY,
                    $attributes['no_of_questions']
                );
            },
            'no_of_medium_questions' => function (array $attributes) {
                return $this->calcNoOfQuestionsForDifficultyLevel(
                    $attributes['difficulty'],
                    Difficulty::MEDIUM,
                    $attributes['no_of_questions']
                );
            },
            'no_of_hard_questions' => function (array $attributes) {
                return $this->calcNoOfQuestionsForDifficultyLevel(
                    $attributes['difficulty'],
                    Difficulty::HARD,
                    $attributes['no_of_questions']
                );
            },

            'allocated_time' => function (array $attributes) {
                return $attributes['no_of_questions'] + random_int(10, 20);
            },
        ];
    }

    /**
     * if difficulty level is HARD this method is used to decide
     * no of questions for each difficulty level
     */
    public function calcNoOfQuestionsForDifficultyLevel(
        Difficulty $difficulty,
        Difficulty $type,
        int $totalQuestions
    ): int {
        if ($difficulty->value !== Difficulty::HARD->value) {
            if ($difficulty->value === $type->value) {
                return $totalQuestions;
            }

            return 0;
        }

        $noOfLevels = count(Difficulty::cases());

        $questionsPerLevel = (int) floor($totalQuestions / $noOfLevels);

        return match ($type->value) {
            Difficulty::EASY->value, Difficulty::MEDIUM->value => $questionsPerLevel,
            Difficulty::HARD->value => $totalQuestions - ($questionsPerLevel * ($noOfLevels - 1)),
        };
    }
}
