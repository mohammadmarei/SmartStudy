<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudyPlanController;

Route::get('/study-plans', [StudyPlanController::class, 'index']);
Route::post('/study-plans', [StudyPlanController::class, 'store']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');