<?php

namespace App\Http\Controllers;

use App\Models\PhishingScanLog;
use App\Models\QuizResult;
use App\Models\RiskScore;
use App\Models\UserLessonProgress;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // --- Risk Score ---
        $latestRiskScore = RiskScore::where('user_id', $userId)
                                    ->latest()
                                    ->first();

        // --- Quiz Stats ---
        $quizStats = QuizResult::where('user_id', $userId)->get();
        $quizSummary = [
            'total_taken'    => $quizStats->count(),
            'average_score'  => $quizStats->count() > 0
                                ? round($quizStats->avg('percentage'), 2)
                                : 0,
            'highest_score'  => $quizStats->count() > 0
                                ? $quizStats->max('percentage')
                                : 0,
            'latest_result'  => $quizStats->sortByDesc('taken_at')->first(),
        ];

        // --- Lesson Progress ---
        $lessonProgress = UserLessonProgress::where('user_id', $userId)->get();
        $lessonSummary = [
            'total_completed' => $lessonProgress->where('is_completed', true)->count(),
            'in_progress'     => $lessonProgress->where('is_completed', false)->count(),
        ];

        // --- Scan Activity ---
        $scanLogs = PhishingScanLog::where('user_id', $userId)->get();
        $scanSummary = [
            'total_scans'    => $scanLogs->count(),
            'high_risk'      => $scanLogs->where('risk_level', 'high')->count(),
            'medium_risk'    => $scanLogs->where('risk_level', 'medium')->count(),
            'low_risk'       => $scanLogs->where('risk_level', 'low')->count(),
            'recent_scans'   => $scanLogs->sortByDesc('scanned_at')
                                            ->take(5)
                                            ->values(),
        ];

        // --- Recent Activity ---
        $recentQuizzes = QuizResult::where('user_id', $userId)
                                    ->with('quiz')
                                    ->latest()
                                    ->take(3)
                                    ->get();

        return response()->json([
            'user'            => $request->user()->load('profile'),
            'risk_score'      => $latestRiskScore,
            'quiz_summary'    => $quizSummary,
            'lesson_summary'  => $lessonSummary,
            'scan_summary'    => $scanSummary,
            'recent_quizzes'  => $recentQuizzes,
        ]);
    }
}
