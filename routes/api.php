<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\StudyPlanController;
use App\Http\Controllers\StudyPlanAIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AiContentController;

Route::middleware('auth:sanctum')->get('/study-plans', [StudyPlanController::class, 'index']);
Route::middleware('auth:sanctum')->post('/study-plans', [StudyPlanController::class, 'store']);
//بتجيب اليوزر اللي عامل لوجن
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//بتسجل دخول اليوزر
Route::post('/login', [AuthController::class, 'login']);
Route::get('/quizzes/{subjectId}', [QuizController::class, 'showByDifficulty']);
Route::post('/quizzes/{quizId}/submit',[QuizController::class,'submitQuiz'])->middleware('auth:sanctum');
Route::get('/results/{resultId}', [ResultController::class, 'getResults'])->middleware('auth:sanctum');
Route::get('/results/{resultId}/details', [ResultController::class, 'getResultDetails'])->middleware('auth:sanctum');
Route::post('/register',[AuthController::class,'register']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


Route::middleware('auth:sanctum')->get('/profile', [ProfileController::class, 'show']);
Route::middleware('auth:sanctum')->post('/profile', [ProfileController::class, 'upsert']);

Route::middleware('auth:sanctum')->put('/user', [UserController::class, 'update']);
Route::middleware('auth:sanctum')->put('/user/password', [UserController::class, 'changePassword']);
Route::middleware('auth:sanctum')->delete('/user', [UserController::class, 'destroy']);




Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->get('/subjects', [SubjectController::class, 'index']);
Route::middleware('auth:sanctum')->post('/subjects', [SubjectController::class, 'store']);
Route::middleware('auth:sanctum')->get('/subjects/{id_subject}', [SubjectController::class, 'show']);
Route::middleware('auth:sanctum')->put('/subjects/{id}', [SubjectController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/subjects/{id}', [SubjectController::class, 'destroy']);

Route::middleware('auth:sanctum')->post('/files', [FileController::class, 'store']);
Route::middleware('auth:sanctum')->delete('/files/{file}', [FileController::class, 'destroy']);

Route::middleware('auth:sanctum')->get('/ai/summaries', [AiContentController::class, 'indexSummaries']);
Route::middleware('auth:sanctum')->get('/ai/quizzes', [AiContentController::class, 'indexQuizzes']);
Route::middleware('auth:sanctum')->post('/ai/summaries', [AiContentController::class, 'generateSummary']);
Route::middleware('auth:sanctum')->post('/ai/quizzes', [AiContentController::class, 'generateQuiz']);
Route::middleware('auth:sanctum')->get('/study-plans', [StudyPlanController::class, 'index']);
Route::middleware('auth:sanctum')->post('/study-plans', [StudyPlanController::class, 'store']);

Route::middleware('auth:sanctum')->get('/analytics', [PerformanceController::class, 'index']);
Route::middleware('auth:sanctum')->post('/generate-study-plan', [StudyPlanAIController::class, 'generate']);
