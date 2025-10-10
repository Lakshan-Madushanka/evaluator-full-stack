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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id()->primary();

            $table->unsignedBigInteger('user_questionnaire_id');

            $table->unsignedSmallInteger('time_taken');
            $table->smallInteger('correct_answers')->nullable();
            $table->smallInteger('no_of_answered_questions')->nullable();
            $table->float('marks_percentage')->nullable();
            $table->float('total_points_earned')->nullable();
            $table->float('total_points_allocated')->nullable();

            $table->timestamps();

            $table->foreign('user_questionnaire_id')
                ->on('user_questionnaire')
                ->references('id')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluation');
    }
};
