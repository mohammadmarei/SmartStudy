<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudyPlanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ResultController;
Route::get('/study-plans', [StudyPlanController::class, 'index']);
Route::post('/study-plans', [StudyPlanController::class, 'store']);
//بتجيب اليوزر اللي عامل لوجن
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//بتسجل دخول اليوزر
Route::post('/login', [AuthController::class, 'login']);
Route::get('/quizzes/{subjectId}', [QuizController::class, 'showByDifficulty']);
Route::post('/quizzes/{quizId}/submit',[QuizController::class,'submitQuiz'])->middleware('auth:sanctum');
Route::get('/results/{resultId}', [ResultController::class, 'getResults'])->middleware('auth:sanctum');
Route::get('/results/{resultId}/details', [ResultController::class, 'getResultDetails'])->middleware('auth:sanctum');