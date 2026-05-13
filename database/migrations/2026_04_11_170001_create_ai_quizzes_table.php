<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_quizzes', function (Blueprint $table) {
    $table->id();

    $table->foreignId('subject_id')
        ->constrained()
        ->onDelete('cascade');

    $table->foreignId('content_id')
        ->constrained('files')
        ->onDelete('cascade');

    $table->string('title');

    $table->enum('difficulty', ['easy', 'medium', 'hard'])
        ->default('medium');

    $table->unsignedInteger('time_limit')->nullable(); 
      $table->foreignId('job_id')
                ->constrained('ai_jobs')
                ->onDelete('cascade');
    /*         $table->foreignId('subject_id')->constrained()->onDelete('cascade'); */

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_quizzes');
    }
};
