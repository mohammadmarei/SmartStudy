<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\SubjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudyPlanController;

Route::get('/study-plans', [StudyPlanController::class, 'index']);
Route::post('/study-plans', [StudyPlanController::class, 'store']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/login',[AuthController::class,'login']); 
Route::post('/register',[AuthController::class,'register']); 
Route::middleware('auth:sanctum')->get('/subjects', [SubjectController::class, 'index']);
Route::middleware('auth:sanctum')->post('/subjects', [SubjectController::class, 'store']);
Route::middleware('auth:sanctum')->get('/subjects/{id_subject}', [SubjectController::class, 'show']);
Route::middleware('auth:sanctum')->put('/subjects/{id}', [SubjectController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/subjects/{id}', [SubjectController::class, 'destroy']);

Route::middleware('auth:sanctum')->post('/files', [FileController::class, 'store']);
Route::middleware('auth:sanctum')->delete('/files/{file}', [FileController::class, 'destroy']);