<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PhishingController;
use App\Http\Controllers\UrlAnalyzerController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\RiskScoreController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\AdminController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/lessons',       [LessonController::class, 'index']);
Route::get('/lessons/{id}',  [LessonController::class, 'show']);
Route::post('/scan/email', [PhishingController::class, 'scanEmail']);
Route::post('/scan/url', [UrlAnalyzerController::class, 'analyze']);
Route::get('/quizzes',      [QuizController::class, 'index']);
Route::get('/quizzes/{id}', [QuizController::class, 'show']);
Route::get('/simulations',      [SimulationController::class, 'index']);
Route::get('/simulations/{id}', [SimulationController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',         [AuthController::class,  'logout']);
    Route::get('/user',            [AuthController::class,  'user']);
    Route::get('/profile',         [ProfileController::class, 'show']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/lessons/{id}/complete', [LessonController::class, 'complete']);
    Route::get('/lessons/progress',       [LessonController::class, 'progress']);
    Route::post('/lessons',               [LessonController::class, 'store']);
    Route::get('/scan/history', [PhishingController::class, 'history']);
    Route::post('/quizzes',               [QuizController::class, 'store']);
    Route::post('/quizzes/{id}/submit',   [QuizController::class, 'submit']);
    Route::get('/quizzes/history',        [QuizController::class, 'history']);
    Route::post('/risk-score/generate', [RiskScoreController::class, 'generate']);
    Route::get('/risk-score/latest',    [RiskScoreController::class, 'latest']);
    Route::get('/risk-score/history',   [RiskScoreController::class, 'history']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/recommendations', [RecommendationController::class, 'index']);
    Route::post('/simulations',                  [SimulationController::class, 'store']);
    Route::post('/simulations/{id}/interact',    [SimulationController::class, 'interact']);
    Route::get('/simulations/history',           [SimulationController::class, 'history']);
    Route::post('/reports/generate', [ReportController::class, 'generate']);
    Route::get('/reports/history',   [ReportController::class, 'history']);
});

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard',          [AdminController::class, 'dashboard']);

    // Users
    Route::get('/users',              [AdminController::class, 'users']);
    Route::get('/users/{id}',         [AdminController::class, 'showUser']);
    Route::put('/users/{id}',         [AdminController::class, 'updateUser']);
    Route::delete('/users/{id}',      [AdminController::class, 'deleteUser']);
    Route::post('/users/{id}/admin',  [AdminController::class, 'makeAdmin']);

    // Lessons
    Route::get('/lessons',            [AdminController::class, 'lessons']);
    Route::put('/lessons/{id}',       [AdminController::class, 'updateLesson']);
    Route::delete('/lessons/{id}',    [AdminController::class, 'deleteLesson']);

    // Quizzes
    Route::get('/quizzes',            [AdminController::class, 'quizzes']);
    Route::delete('/quizzes/{id}',    [AdminController::class, 'deleteQuiz']);

    // Logs & Reports
    Route::get('/scan-logs',          [AdminController::class, 'scanLogs']);
    Route::get('/reports',            [AdminController::class, 'reports']);
});
