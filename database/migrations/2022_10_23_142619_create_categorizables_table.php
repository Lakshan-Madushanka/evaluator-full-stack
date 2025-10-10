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
        Schema::create('categorizables', function (Blueprint $table) {
            $table->id()->primary();

            $table->unsignedTinyInteger('category_id');

            // Polymorphic relationship columns
            $table->unsignedTinyInteger('categorizable_type')->index();
            $table->unsignedBigInteger('categorizable_id')->index();

            $table->timestamps();

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
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
        Schema::dropIfExists('categorizeble');
    }
};
