<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\StudyPlan;
use App\Models\Performance;
use App\Models\Recommendation;
use App\Models\WeakArea;

class FeatureFiveSeeder extends Seeder
{
    public function run(): void
    {
        $roleId = DB::table('roles')->insertGetId([
        'name' => 'student',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $userId = DB::table('users')->insertGetId([
        'role_id' => $roleId,
        'full_name' => 'Test Student',
        'username' => 'teststudent',
        'email' => 'student1@example.com',
        'password_hash' => bcrypt('12345678'),
        'agreed_to_terms' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

        $subject1 = DB::table('subjects')->insertGetId([
            'user_id' => $userId,
            'name' => 'Mathematics',
            'color' => '#3a6cf4',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subject2 = DB::table('subjects')->insertGetId([
            'user_id' => $userId,
            'name' => 'Physics',
            'color' => '#10b981',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subject3 = DB::table('subjects')->insertGetId([
            'user_id' => $userId,
            'name' => 'Chemistry',
            'color' => '#f59e0b',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $subject4 = DB::table('subjects')->insertGetId([
            'user_id' => $userId,
            'name' => 'Biology',
            'color' => '#8b5cf6',
            'created_at' => now(),
            'updated_at' => now(),
     ]);
        StudyPlan::insert([
            [
                'user_id' => $userId,
                'subject_id' => $subject1,
                'goal' => 'Review Calculus derivatives and integration',
                'start_date' => '2026-04-12',
                'end_date' => '2026-04-15',
                'status' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'subject_id' => $subject2,
                'goal' => 'Solve Thermodynamics quiz',
                'start_date' => '2026-04-12',
                'end_date' => '2026-04-13',
                'status' => 'Done',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'subject_id' => $subject3,
                'goal' => 'Read Organic Chemistry summary',
                'start_date' => '2026-04-13',
                'end_date' => '2026-04-16',
                'status' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'subject_id' => $subject4,
                'goal' => 'Review Cell Biology weak topics',
                'start_date' => '2026-04-14',
                'end_date' => '2026-04-17',
                'status' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Performance::insert([
            [
                'user_id' => $userId,
                'subject_id' => $subject1,
                'average_score' => 82,
                'success_rate' => 78,
                'weak_topics_count' => 1,
                'last_updated' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'subject_id' => $subject2,
                'average_score' => 65,
                'success_rate' => 60,
                'weak_topics_count' => 2,
                'last_updated' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'subject_id' => $subject3,
                'average_score' => 50,
                'success_rate' => 45,
                'weak_topics_count' => 3,
                'last_updated' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'subject_id' => $subject4,
                'average_score' => 90,
                'success_rate' => 88,
                'weak_topics_count' => 0,
                'last_updated' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Recommendation::insert([
            [
                'user_id' => $userId,
                'message' => 'Focus on Organic Chemistry weak topics',
                'type' => 'weak_area',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'message' => 'Review Calculus derivatives before next quiz',
                'type' => 'review',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'message' => 'Retake Thermodynamics quiz to improve score',
                'type' => 'quiz',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'message' => 'Explore additional resources for Reaction Mechanisms',
                'type' => 'resource',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

                
        ]);

        WeakArea::insert([
            [
                'user_id' => $userId,
                'subject_id' => $subject3,
                'topic_name' => 'Organic Chemistry',
                'weakness_level' => 'High',
                'times_mistaken' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'subject_id' => $subject1,
                'topic_name' => 'Integration',
                'weakness_level' => 'Medium',
                'times_mistaken' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'subject_id' => $subject2,
                'topic_name' => 'Thermodynamics',
                'weakness_level' => 'Medium',
                'times_mistaken' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId,
                'subject_id' => $subject3,
                'topic_name' => 'Reaction Mechanisms',
                'weakness_level' => 'High',
                'times_mistaken' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'user_id' => $userId,
                'subject_id' => $subject1,
                'topic_name' => 'Derivatives',
                'weakness_level' => 'Low',
                'times_mistaken' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'user_id' => $userId,
                'subject_id' => $subject2,
                'topic_name' => 'Kinematics',
                'weakness_level' => 'Low',
                'times_mistaken' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'user_id' => $userId,
                'subject_id' => $subject4,
                'topic_name' => 'Genetics',
                'weakness_level' => 'Low',
                'times_mistaken' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}