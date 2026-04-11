<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_key_summaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('summary_id')
                ->constrained('ai_summaries')
                ->onDelete('cascade');

            $table->string('concept', 255);
            $table->enum('concept_type', ['concept', 'keyword']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_key_summaries');
    }
};
