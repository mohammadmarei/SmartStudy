<?php

namespace App\Http\Controllers;

use App\Services\StudyPlanGeneratorService;
use Illuminate\Http\JsonResponse;

class StudyPlanAIController extends Controller
{
    protected StudyPlanGeneratorService $studyPlanGeneratorService;

    public function __construct(StudyPlanGeneratorService $studyPlanGeneratorService)
    {
        $this->studyPlanGeneratorService = $studyPlanGeneratorService;
    }

    public function generate(): JsonResponse
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        try {
            $plans = $this->studyPlanGeneratorService->generateForUser($userId);

            return response()->json([
                'message' => 'AI study plan generated successfully',
                'data' => $plans
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate AI study plan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}