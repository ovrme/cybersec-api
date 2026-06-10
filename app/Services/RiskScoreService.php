<?php

namespace App\Services;

use App\Models\PhishingScanLog;
use App\Models\QuizResult;
use App\Models\RiskScore;

class RiskScoreService
{
    public function calculate(int $userId): array
    {
        // --- Quiz Score Weight (40%) ---
        $quizResults = QuizResult::where('user_id', $userId)->get();

        if ($quizResults->count() > 0) {
            $avgQuizScore = $quizResults->avg('percentage');
            // Low quiz score = high risk
            // 100% quiz = 0 risk, 0% quiz = 40 risk
            $quizWeight = round((100 - $avgQuizScore) * 0.4, 2);
        } else {
            // No quizzes taken = moderate risk
            $quizWeight = 20;
        }

        // --- Behavior Weight (60%) ---
        $scanLogs = PhishingScanLog::where('user_id', $userId)->get();

        if ($scanLogs->count() > 0) {
            $highRiskScans   = $scanLogs->where('risk_level', 'high')->count();
            $mediumRiskScans = $scanLogs->where('risk_level', 'medium')->count();
            $totalScans      = $scanLogs->count();

            $behaviorWeight = round(
                (($highRiskScans * 2 + $mediumRiskScans) / ($totalScans * 2)) * 60,
                2
            );
        } else {
            // No scans done = moderate risk
            $behaviorWeight = 20;
        }

        // --- Final Score ---
        $finalScore = min(round($quizWeight + $behaviorWeight), 100);

        return [
            'score'           => $finalScore,
            'level'           => $this->getLevel($finalScore),
            'quiz_weight'     => $quizWeight,
            'behavior_weight' => $behaviorWeight,
        ];
    }

    private function getLevel(int $score): string
    {
        if ($score >= 60) return 'high';
        if ($score >= 30) return 'medium';
        return 'low';
    }
}
