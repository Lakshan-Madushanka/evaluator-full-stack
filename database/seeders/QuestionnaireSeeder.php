<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Questionnaire;
use Illuminate\Database\Seeder;

class QuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::all(['id'])
            ->each(function (Category $category) {
                $noOfQuestionnaires = random_int(1, 5);

                $records = Questionnaire::factory()->count($noOfQuestionnaires)->make()->toArray();

                $category->questionnaires()->createMany($records);
            });
    }
}
