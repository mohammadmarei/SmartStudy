<?php

namespace App\Http\Controllers;

use App\Models\StudyPlan;
use Illuminate\Http\Request;

class StudyPlanController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $plans = StudyPlan::with('subject')
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return response()->json($plans);
    }

    public function store(Request $request)
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'goal' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:Pending,Done,Missed',
        ]);

        $validated['user_id'] = $userId;

        $plan = StudyPlan::create($validated);

        return response()->json(
            $plan->load('subject'),
            201
        );
    }
}