<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_answers', function (Blueprint $table) {
            //
              $table->foreign('question_id')
        ->references('id')
        ->on('ai_questions')
        ->onDelete('cascade');
/* 
    $table->foreign('quiz_id')
        ->references('id')
        ->on('ai_quizzes')
        ->onDelete('cascade'); */

    $table->foreign('option_id')
        ->references('id')
        ->on('question_options')
        ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_answers', function (Blueprint $table) {
        $table->dropForeign(['question_id']);
        $table->dropForeign(['quiz_id']);
        $table->dropForeign(['option_id']);
    });
    }
};
