<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaires', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->string('name', 50)->unique();
            $table->tinyInteger('difficulty');
            /*
             *  Single answers type questionnaire contain questions that has
             * one correct answer (is_answers_type_single true)
            */
            $table->boolean('single_answers_type')->default(false);

            $table->smallInteger('no_of_questions');
            $table->smallInteger('no_of_easy_questions');
            $table->smallInteger('no_of_medium_questions');
            $table->smallInteger('no_of_hard_questions');

            $table->unsignedSmallInteger('allocated_time'); // in minutes

            $table->index(['difficulty', 'no_of_questions']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questionnaires');
    }
};
