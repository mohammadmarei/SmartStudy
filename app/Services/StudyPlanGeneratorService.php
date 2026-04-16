<?php

namespace App\Services;

use App\Models\WeakArea;
use App\Models\Performance;
use App\Models\StudyPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StudyPlanGeneratorService
{
    public function generateForUser(int $userId)
    {
        return DB::transaction(function () use ($userId) {
            $weakAreas = WeakArea::where('user_id', $userId)->get();
            $performances = Performance::where('user_id', $userId)->get()->keyBy('subject_id');

            $previousPlans = StudyPlan::where('user_id', $userId)
                ->latest()
                ->get()
                ->groupBy('subject_id');

            $generatedPlans = [];

            foreach ($weakAreas as $weakArea) {
                $performance = $performances->get($weakArea->subject_id);

                $lastGoal = optional($previousPlans->get($weakArea->subject_id)?->first())->goal;

                $priorityScore = $this->calculatePriority($weakArea, $performance);
                $taskType = $this->decideTaskType($weakArea, $lastGoal);

                $generatedPlans[] = [
                    'user_id' => $userId,
                    'subject_id' => $weakArea->subject_id,
                    'goal' => $this->buildGoalText($taskType, $weakArea->topic_name),
                    'priority_score' => $priorityScore,
                ];
            }

            usort($generatedPlans, fn ($a, $b) => $b['priority_score'] <=> $a['priority_score']);

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

    private function calculatePriority($weakArea, $performance): int
    {
        $score = 0;

        if ($weakArea->weakness_level === 'High') {
            $score += 50;
        } elseif ($weakArea->weakness_level === 'Medium') {
            $score += 30;
        } else {
            $score += 10;
        }

        $score += $weakArea->times_mistaken * 5;

        if ($performance) {
            $score += (100 - $performance->success_rate);
        }

        return $score;
    }

    private function decideTaskType($weakArea, ?string $lastGoal = null): string
    {
        $types = ['Review', 'Quiz', 'Summary'];

        if ($weakArea->weakness_level === 'High') {
            $preferred = 'Review';
        } elseif ($weakArea->times_mistaken >= 4) {
            $preferred = 'Quiz';
        } else {
            $preferred = 'Summary';
        }

        if ($lastGoal) {
            foreach ($types as $type) {
                if (stripos($lastGoal, $type) !== false) {
                    $types = array_values(array_filter($types, fn ($t) => $t !== $type));
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

    private function buildGoalText(string $taskType, string $topic): string
    {
        return match ($taskType) {
            'Review' => "Review {$topic} concepts",
            'Quiz' => "Take a quiz on {$topic}",
            'Summary' => "Read summary of {$topic}",
            default => "Study {$topic}",
        };
    }
}