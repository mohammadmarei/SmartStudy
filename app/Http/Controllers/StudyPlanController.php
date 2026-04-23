<?php

namespace App\Http\Controllers;

use App\Models\StudyPlan;
use Illuminate\Http\Request;

class StudyPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = StudyPlan::with('subject');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $plans = $query->latest()->get();

        return response()->json($plans);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'goal' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:Pending,Done,Missed',
        ]);

        $plan = StudyPlan::create($validated);

        return response()->json(
            $plan->load('subject'),
            201
        );
    }
}