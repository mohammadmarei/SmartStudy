<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Performance;
use App\Models\Recommendation;
use App\Models\WeakArea;

class PerformanceController extends Controller
{
    public function index()
    {
        $performances = Performance::with('subject')->get();
        $recommendations = Recommendation::all();
        $weakAreas = WeakArea::with('subject')->get();

        $averageScore = round($performances->avg('average_score') ?? 0, 2);
        $successRate = round($performances->avg('success_rate') ?? 0, 2);
        $weakTopicsCount = $weakAreas->count();
        $completedQuizzes = 24;

        $subjectPerformance = $performances->map(function ($item) {
            return [
                'subject_id' => $item->subject_id,
                'subject_name' => $item->subject?->name,
                'subject_color' => $item->subject?->color,
                'score' => $item->average_score,
                'success_rate' => $item->success_rate,
            ];
        });

        $progressOverTime = [
            ['date' => 'Week 1', 'score' => 65],
            ['date' => 'Week 2', 'score' => 72],
            ['date' => 'Week 3', 'score' => 68],
            ['date' => 'Week 4', 'score' => 78],
            ['date' => 'Week 5', 'score' => 82],
            ['date' => 'Week 6', 'score' => 85],
        ];

        return response()->json([
            'stats' => [
                'success_rate' => $successRate,
                'average_score' => $averageScore,
                'completed_quizzes' => $completedQuizzes,
                'weak_topics' => $weakTopicsCount,
            ],
            'subject_performance' => $subjectPerformance,
            'progress_over_time' => $progressOverTime,
            'recommendations' => $recommendations,
            'weak_areas' => $weakAreas,
        ]);
    }
}