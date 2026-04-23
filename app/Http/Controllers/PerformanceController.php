<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\WeakArea;

class PerformanceController extends Controller
{
    public function index()
    {
        $userId = 1;

        $results = Result::with('quiz.subject')
            ->where('user_id', $userId)
            ->get()
            ->filter(fn($result) => $result->quiz && $result->quiz->subject)
            ->values();

        $weakAreas = WeakArea::with('subject')
            ->where('user_id', $userId)
            ->get();

        $completedQuizzes = $results->count();

        $subjectPerformance = $results
            ->groupBy(fn($result) => $result->quiz->subject_id)
            ->map(function ($group) {
                $first = $group->first();
                $subject = $first->quiz->subject;

                $averageScore = round($group->avg('score'), 2);

                $successRate = round(
                    $group->filter(fn($item) => $item->score >= 50)->count() * 100 / max($group->count(), 1),
                    2
                );

                return [
                    'subject_id' => $subject->id,
                    'subject_name' => $subject->name,
                    'subject_color' => $subject->color,
                    'score' => $averageScore,
                    'success_rate' => $successRate,
                ];
            })
            ->values();

        $averageScore = round($subjectPerformance->avg('score') ?? 0, 2);
        $successRate = round($subjectPerformance->avg('success_rate') ?? 0, 2);
        $weakTopicsCount = $weakAreas->count();

        $progressOverTime = $results
            ->sortBy('created_at')
            ->values()
            ->map(function ($result, $index) {
                return [
                    'date' => 'Quiz ' . ($index + 1),
                    'score' => $result->score,
                ];
            });

        $recommendations = $this->generateRecommendations($subjectPerformance, $weakAreas);

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

    private function generateRecommendations($subjectPerformance, $weakAreas)
    {
        $recommendations = collect();

        foreach ($subjectPerformance as $subject) {
            if ($subject['score'] < 50) {
                $recommendations->push([
                    'title' => 'Focus on Weak Areas',
                    'message' => "Your performance in {$subject['subject_name']} is low. Review the key weak topics first.",
                    'type' => 'weak_area',
                ]);
            } elseif ($subject['score'] < 70) {
                $recommendations->push([
                    'title' => 'Practice More Quizzes',
                    'message' => "Take more quizzes in {$subject['subject_name']} to improve your score.",
                    'type' => 'quiz',
                ]);
            } else {
                $recommendations->push([
                    'title' => 'Review Important Topics',
                    'message' => "Keep revising important concepts in {$subject['subject_name']} to maintain your performance.",
                    'type' => 'review',
                ]);
            }
        }

        foreach ($weakAreas as $weakArea) {
            if ($weakArea->weakness_level === 'High') {
                $recommendations->push([
                    'title' => 'Focus on Weak Areas',
                    'message' => "Focus on {$weakArea->topic_name} in {$weakArea->subject?->name}.",
                    'type' => 'weak_area',
                ]);
            }
        }

        return $recommendations->unique('message')->values()->take(6);
    }
}