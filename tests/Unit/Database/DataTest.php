<?php

use App\Models\Answer;
use App\Models\Category;
use App\Models\Question;
use Database\Data\ProgrammingData;

use function Pest\Laravel\assertDatabaseCount;

afterEach(function () {
    $this->seed = true;

    Artisan::call('migrate:fresh', ['--force' => true]);
    Artisan::call('db:seed', ['--force' => true]);
});

it('can build questions', function () {
    $this->seed = false;

    ProgrammingData::seedQuestions();

    assertDatabaseCount((new Question)->getTable(), 29);
    assertDatabaseCount((new Answer)->getTable(), 116);
    assertDatabaseCount((new Category)->getTable(), 2);
    assertDatabaseCount('categorizables', 62);
});
