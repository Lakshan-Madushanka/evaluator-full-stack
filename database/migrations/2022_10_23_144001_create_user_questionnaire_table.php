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
        Schema::create('user_questionnaire', function (Blueprint $table) {
            $table->id();

            $table->unsignedSmallInteger('questionnaire_id')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->tinyInteger('attempts')->default(0);
            $table->string('code')->unique();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('expires_at');
            $table->json('answers')->nullable();

            $table->timestamps();

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
        Schema::dropIfExists('user_questionnaire');
    }
};
