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
        Schema::create('questionnaire_question', function (Blueprint $table) {
            $table->id();

            $table->unsignedSmallInteger('questionnaire_id');
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();

            $table->float('marks')->default(1);

            $table->foreign('questionnaire_id')
                ->on('questionnaires')
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
        Schema::dropIfExists('table_questionnaire_question');
    }
};
