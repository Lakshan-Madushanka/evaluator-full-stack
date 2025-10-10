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
        Schema::table('user_questionnaire', function (Blueprint $table) {
            $table
                ->unsignedBigInteger('questionnaire_team_id')
                ->nullable()
                ->default(null)
                ->after('user_id');

            $table->foreign('questionnaire_team_id')->references('id')->on('questionnaire_team')->nullOnDelete();
        });
    }
};
