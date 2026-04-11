<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AiTablesMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_tables_accept_inserts_and_foreign_keys(): void
    {
        $roleId = DB::table('roles')->insertGetId([
            'name' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userId = DB::table('users')->insertGetId([
            'role_id' => $roleId,
            'full_name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => 'x',
            'agreed_to_terms' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subjectId = DB::table('subjects')->insertGetId([
            'user_id' => $userId,
            'name' => 'Math',
            'color' => '#000000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $materialId = DB::table('files')->insertGetId([
            'subject_id' => $subjectId,
            'file_name' => 'notes.pdf',
            'file_path' => '/storage/notes.pdf',
            'file' => 'pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $jobId = DB::table('ai_jobs')->insertGetId([
            'material_id' => $materialId,
            'subject_id' => $subjectId,
            'user_id' => $userId,
            'model_used' => 'gemini-1.5-pro',
            'status' => 'pending',
            'error_message' => null,
            'retry_count' => 0,
            'created_at' => now(),
            'completed_at' => null,
        ]);

        $summaryId = DB::table('ai_summaries')->insertGetId([
            'job_id' => $jobId,
            'summary_text' => 'Summary of the material.',
        ]);

        DB::table('ai_key_summaries')->insert([
            'summary_id' => $summaryId,
            'concept' => 'quadratic formula',
            'concept_type' => 'concept',
        ]);

        $quizId = DB::table('ai_quizzes')->insertGetId([
            'job_id' => $jobId,
            'subject_id' => $subjectId,
            'difficulty' => 'medium',
        ]);

        $questionId = DB::table('ai_questions')->insertGetId([
            'quiz_id' => $quizId,
            'question_text' => 'What is 2+2?',
            'explanation' => 'Basic arithmetic.',
        ]);

        DB::table('question_options')->insert([
            'question_id' => $questionId,
            'option_label' => 'A',
            'option_text' => '4',
            'is_correct' => true,
        ]);

        $this->assertDatabaseHas('ai_jobs', ['id' => $jobId, 'status' => 'pending']);
        $this->assertDatabaseHas('ai_summaries', ['id' => $summaryId]);
        $this->assertDatabaseHas('ai_key_summaries', ['concept' => 'quadratic formula']);
        $this->assertDatabaseHas('ai_quizzes', ['id' => $quizId]);
        $this->assertDatabaseHas('ai_questions', ['id' => $questionId]);
        $this->assertDatabaseHas('question_options', ['option_label' => 'A', 'is_correct' => 1]);
    }

    public function test_deleting_ai_job_cascades_to_children(): void
    {
        $roleId = DB::table('roles')->insertGetId([
            'name' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userId = DB::table('users')->insertGetId([
            'role_id' => $roleId,
            'full_name' => 'Test User',
            'username' => 'testuser2',
            'email' => 'test2@example.com',
            'password_hash' => 'x',
            'agreed_to_terms' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subjectId = DB::table('subjects')->insertGetId([
            'user_id' => $userId,
            'name' => 'Physics',
            'color' => '#111111',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $materialId = DB::table('files')->insertGetId([
            'subject_id' => $subjectId,
            'file_name' => 'lab.pdf',
            'file_path' => '/storage/lab.pdf',
            'file' => 'pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $jobId = DB::table('ai_jobs')->insertGetId([
            'material_id' => $materialId,
            'subject_id' => $subjectId,
            'user_id' => $userId,
            'status' => 'completed',
            'retry_count' => 0,
            'created_at' => now(),
            'completed_at' => now(),
        ]);

        $summaryId = DB::table('ai_summaries')->insertGetId([
            'job_id' => $jobId,
            'summary_text' => 'Cascade test.',
        ]);

        $quizId = DB::table('ai_quizzes')->insertGetId([
            'job_id' => $jobId,
            'subject_id' => $subjectId,
            'difficulty' => 'easy',
        ]);

        DB::table('ai_jobs')->where('id', $jobId)->delete();

        $this->assertDatabaseMissing('ai_summaries', ['id' => $summaryId]);
        $this->assertDatabaseMissing('ai_quizzes', ['id' => $quizId]);
    }
}
