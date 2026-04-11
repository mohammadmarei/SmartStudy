<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weak_areas', function (Blueprint $table) {
            $table->id(); // weak_area_id

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');

            $table->string('topic_name');
            $table->string('weakness_level');
            $table->integer('times_mistaken');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weak_areas');
    }
};