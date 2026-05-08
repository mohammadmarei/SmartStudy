<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StudyPlan;
use Illuminate\Http\Request;

class StudyPlanController extends Controller
{
    public function index()
    {
        return response()->json(StudyPlan::all());
    }

    public function store(Request $request)
    {
        $plan = StudyPlan::create($request->all());
        return response()->json($plan, 201);
    }
}