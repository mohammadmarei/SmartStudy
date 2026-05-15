<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateAiQuizRequest;
use App\Http\Requests\GenerateAiSummaryRequest;
use App\Models\AiQuiz;
use App\Models\AiSummary;
use App\Models\File;
use App\Models\Subject;
use App\Services\AiContentGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AiContentController extends Controller
{
    public function __construct(private readonly AiContentGenerator $generator) {}

    public function indexSummaries(): JsonResponse
    {
        $userId = Auth::id();

        $rows = AiSummary::query()
            ->with(['job.subject', 'job.material', 'keyPoints'])
            ->whereHas('job', function ($q) use ($userId): void {
                $q->where('user_id', $userId)->where('status', 'completed');
            })
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->map(function (AiSummary $s) {
                $job = $s->job;

                return [
                    'id' => $s->id,
                    'summary_text' => $s->summary_text,
                    'key_points' => $s->keyPoints->map(fn ($k) => [
                        'id' => $k->id,
                        'concept' => $k->concept,
                        'concept_type' => $k->concept_type,
                    ])->values()->all(),
                    'subject' => $job?->subject ? [
                        'id' => $job->subject->id,
                        'name' => $job->subject->name,
                        'color' => $job->subject->color,
                    ] : null,
                    'material' => $job?->material ? [
                        'id' => $job->material->id,
                        'file_name' => $job->material->file_name,
                    ] : null,
                    'created_at' => $job?->completed_at?->toIso8601String()
                        ?? $job?->created_at?->toIso8601String(),
                ];
            });

        return response()->json(['data' => $rows]);
    }

    public function indexQuizzes(): JsonResponse
    {
        $userId = Auth::id();

        $rows = AiQuiz::query()
            ->with(['subject', 'job.material', 'questions.options'])
            ->whereHas('job', function ($q) use ($userId): void {
                $q->where('user_id', $userId)->where('status', 'completed');
            })
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->map(function (AiQuiz $quiz) {
                $job = $quiz->job;

                $questions = $quiz->questions->map(function ($q) {
                    return [
                        'id' => $q->id,
                        'quiz_id' => $q->quiz_id,
                        'question_text' => $q->question_text,
                        'explanation' => $q->explanation,
                        'options' => $q->options->map(fn ($o) => [
                            'id' => $o->id,
                            'question_id' => $o->question_id,
                            'option_label' => $o->option_label,
                            'option_text' => $o->option_text,
                            'is_correct' => (bool) $o->is_correct,
                        ])->values()->all(),
                    ];
                })->values()->all();

                return [
                    'id' => $quiz->id,
                    'difficulty' => $quiz->difficulty,
                    'subject' => $quiz->subject ? [
                        'id' => $quiz->subject->id,
                        'name' => $quiz->subject->name,
                        'color' => $quiz->subject->color,
                    ] : null,
                    'material' => $job?->material ? [
                        'id' => $job->material->id,
                        'file_name' => $job->material->file_name,
                    ] : null,
                    'question_count' => count($questions),
                    'questions' => $questions,
                    'created_at' => $job?->completed_at?->toIso8601String()
                        ?? $job?->created_at?->toIso8601String(),
                ];
            });

        return response()->json(['data' => $rows]);
    }

    public function generateSummary(GenerateAiSummaryRequest $request): JsonResponse
    {
        $userId = Auth::id();

        /** @var Subject $subject */
        $subject = Subject::where('id', $request->integer('subject_id'))
            ->where('user_id', $userId)
            ->firstOrFail();

        /** @var File $material */
        $material = File::where('id', $request->integer('material_id'))
            ->where('subject_id', $subject->id)
            ->firstOrFail();

        $res = $this->generator->generateSummary(
            $subject,
            $material,
            (int) ($request->input('key_points', 10)),
            $request->input('model')
        );

        return response()->json([
            'job' => $res['job'],
            'summary' => $res['summary'],
            'key_points' => $res['key_points'],
        ], 201);
    }

    public function generateQuiz(GenerateAiQuizRequest $request): JsonResponse
    {
        $userId = Auth::id();

        /** @var Subject $subject */
        $subject = Subject::where('id', $request->integer('subject_id'))
            ->where('user_id', $userId)
            ->firstOrFail();

        /** @var File $material */
        $material = File::where('id', $request->integer('material_id'))
            ->where('subject_id', $subject->id)
            ->firstOrFail();

        $res = $this->generator->generateQuiz(
            $subject,
            $material,
            (string) $request->input('difficulty', 'medium'),
            (int) ($request->input('question_count', 10)),
            $request->input('model')
        );

        $quiz = $res['quiz']->load('questions.options');

        return response()->json([
            'job' => $res['job'],
            'quiz' => $quiz,
        ], 201);
    }
}

