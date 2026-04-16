<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StudyPlan;
use Illuminate\Http\Request;

class StudyPlanController extends Controller
{
    public function index()
    {
        $plans = StudyPlan::with('subject')->get();

        return response()->json($plans);
    }

    public function store(Request $request)
    {
        $plan = StudyPlan::create($request->all());

        return response()->json(
            $plan->load('subject'),
            201
        );
    }
}