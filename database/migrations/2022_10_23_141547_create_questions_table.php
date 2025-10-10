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
        Schema::create('questions', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('pretty_id', 25);

            // if answers type is single only one answer is exist and users are allowed to pick one answer
            $table->boolean('is_answers_type_single')->default(false);
            $table->tinyInteger('difficulty')->index();
            $table->text('text')->fulltext();
            $table->tinyInteger('no_of_answers');

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
        Schema::dropIfExists('questions');
    }
};
