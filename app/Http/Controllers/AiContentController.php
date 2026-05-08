<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateAiQuizRequest;
use App\Http\Requests\GenerateAiSummaryRequest;
use App\Models\File;
use App\Models\Subject;
use App\Services\AiContentGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AiContentController extends Controller
{
    public function __construct(private readonly AiContentGenerator $generator) {}

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

