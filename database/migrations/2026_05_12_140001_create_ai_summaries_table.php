<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_summaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_id')
                ->constrained('ai_jobs')
                ->onDelete('cascade');

            $table->text('summary_text');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_summaries');
    }
};
