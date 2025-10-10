<?php

use App\Models\Team;
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
        Schema::create('questionnaire_team', function (Blueprint $table) {
            $table->id()->primary();

            $table->unsignedSmallInteger('questionnaire_id')->nullable();
            $table->foreignIdFor(Team::class)->nullable()->constrained()->nullOnDelete();

            $table->timestamps();

            $table->foreign('questionnaire_id')
                ->on('questionnaires')
                ->references('id')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questionnaire_team');
    }
};
