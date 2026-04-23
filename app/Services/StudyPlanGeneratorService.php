<?php

namespace App\Services;

use App\Models\Result;
use App\Models\StudyPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StudyPlanGeneratorService
{
    public function generateForUser(int $userId)
    {
        return DB::transaction(function () use ($userId) {
            $results = Result::with('quiz.subject')
                ->where('user_id', $userId)
                ->get()
                ->filter(fn($result) => $result->quiz && $result->quiz->subject)
                ->values();

            if ($results->isEmpty()) {
                StudyPlan::where('user_id', $userId)->delete();

                return collect([]);
            }

            $groupedBySubject = $results->groupBy(fn($result) => $result->quiz->subject_id);

            $previousPlans = StudyPlan::where('user_id', $userId)
                ->latest()
                ->get()
                ->groupBy('subject_id');

            $generatedPlans = [];

            foreach ($groupedBySubject as $subjectId => $subjectResults) {
                $subject = $subjectResults->first()->quiz->subject;

                $averageScore = round($subjectResults->avg('score'), 2);

                $successRate = round(
                    $subjectResults->filter(fn($item) => $item->score >= 50)->count() * 100 / max($subjectResults->count(), 1),
                    2
                );

                $lastGoal = optional($previousPlans->get($subjectId)?->first())->goal;

                $priorityScore = $this->calculatePriority($averageScore, $successRate);
                $taskType = $this->decideTaskType($averageScore, $lastGoal);

                $generatedPlans[] = [
                    'user_id' => $userId,
                    'subject_id' => $subjectId,
                    'goal' => $this->buildGoalText($taskType, $subject->name),
                    'priority_score' => $priorityScore,
                ];
            }

            usort($generatedPlans, fn($a, $b) => $b['priority_score'] <=> $a['priority_score']);

            StudyPlan::where('user_id', $userId)->delete();

            foreach ($generatedPlans as $index => $plan) {
                StudyPlan::create([
                    'user_id' => $plan['user_id'],
                    'subject_id' => $plan['subject_id'],
                    'goal' => $plan['goal'],
                    'start_date' => Carbon::now()->addDays($index)->toDateString(),
                    'end_date' => Carbon::now()->addDays($index + 1)->toDateString(),
                    'status' => 'Pending',
                ]);
            }

            return StudyPlan::with('subject')
                ->where('user_id', $userId)
                ->latest()
                ->get();
        });
    }

    private function calculatePriority(float $averageScore, float $successRate): int
    {
        $score = 0;

        if ($averageScore < 50) {
            $score += 50;
        } elseif ($averageScore < 70) {
            $score += 30;
        } else {
            $score += 10;
        }

        $score += (100 - $successRate);

        return (int) $score;
    }

    private function decideTaskType(float $averageScore, ?string $lastGoal = null): string
    {
        $types = ['Review', 'Quiz', 'Summary'];

        if ($averageScore < 50) {
            $preferred = 'Review';
        } elseif ($averageScore < 70) {
            $preferred = 'Quiz';
        } else {
            $preferred = 'Summary';
        }

        if ($lastGoal) {
            foreach ($types as $type) {
                if (stripos($lastGoal, $type) !== false) {
                    $types = array_values(array_filter($types, fn($t) => $t !== $type));
                    break;
                }
            }

            if (!empty($types) && in_array($preferred, $types)) {
                return $preferred;
            }

            return $types[0] ?? $preferred;
        }

        return $preferred;
    }

    private function buildGoalText(string $taskType, string $subjectName): string
    {
        return match ($taskType) {
            'Review' => "Review key topics in {$subjectName}",
            'Quiz' => "Take a quiz in {$subjectName}",
            'Summary' => "Read summary of {$subjectName}",
            default => "Study {$subjectName}",
        };
    }
}