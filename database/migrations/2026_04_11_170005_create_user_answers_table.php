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
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();
/*             $table->foreignId('user_id')->constrained()->onDelete('cascade'); */
            $table->foreignId('question_id');
            //->constrained("ai_questions")->onDelete('cascade');
          /*   $table->foreignId('quiz_id'); */
            //->constrained("ai_quizzes")->onDelete('cascade');
            $table->foreignId("result_id")->constrained()->onDelete('cascade');
            $table->foreignId("option_id");
            //->constrained("question_options")->onDelete('cascade');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
      /*       $table->unique(
                ['user_id', 'quiz_id', 'question_id', 'result_id'],
                'user_quiz_question_unique'
            ); */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
