<?php

namespace App\Http\Controllers;

use App\Models\PhishingScanLog;
use App\Models\QuizResult;
use App\Models\Report;
use App\Models\RiskScore;
use App\Models\UserLessonProgress;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function generate(Request $request)
    {
        $userId = $request->user()->id;
        $user   = $request->user();

        // Gather all data
        $riskScore = RiskScore::where('user_id', $userId)
                                ->latest()
                                ->first();

        $quizResults = QuizResult::where('user_id', $userId)->get();
        $quizSummary = [
            'total_taken'   => $quizResults->count(),
            'average_score' => $quizResults->count() > 0
                                ? round($quizResults->avg('percentage'), 2) : 0,
            'highest_score' => $quizResults->count() > 0
                                ? $quizResults->max('percentage') : 0,
        ];

        $scanLogs    = PhishingScanLog::where('user_id', $userId)->get();
        $scanSummary = [
            'total_scans' => $scanLogs->count(),
            'high_risk'   => $scanLogs->where('risk_level', 'high')->count(),
            'medium_risk' => $scanLogs->where('risk_level', 'medium')->count(),
            'low_risk'    => $scanLogs->where('risk_level', 'low')->count(),
        ];

        $lessonProgress = UserLessonProgress::where('user_id', $userId)->get();
        $lessonSummary  = [
            'total_completed' => $lessonProgress->where('is_completed', true)->count(),
            'in_progress'     => $lessonProgress->where('is_completed', false)->count(),
        ];

        // Get recommendations based on risk level
        $level           = $riskScore?->level ?? 'medium';
        $recommendations = $this->getRecommendations($level);

        // Build PDF data
        $data = [
            'user'            => ['name' => $user->name, 'email' => $user->email],
            'risk_score'      => $riskScore,
            'quiz_summary'    => $quizSummary,
            'scan_summary'    => $scanSummary,
            'lesson_summary'  => $lessonSummary,
            'recommendations' => $recommendations,
            'generated_at'    => now()->format('Y-m-d H:i:s'),
        ];

        // Generate PDF
        $pdf      = Pdf::loadView('reports.user_report', $data);
        $filename = 'report_user_' . $userId . '_' . now()->format('Ymd_His') . '.pdf';
        $path     = 'reports/' . $filename;

        // Save to storage
        storage_path('app/public/' . $path);
        \Storage::disk('public')->put($path, $pdf->output());

        // Save record to DB
        Report::create([
            'user_id'     => $userId,
            'file_path'   => $path,
            'report_data' => $data,
        ]);

        // Return PDF download
        return $pdf->download($filename);
    }

    public function history(Request $request)
    {
        $reports = Report::where('user_id', $request->user()->id)
                            ->latest()
                            ->get();
        return response()->json($reports);
    }

    private function getRecommendations(string $level): array
    {
        $all = [
            'high' => [
                ['title' => 'Enable Two-Factor Authentication',
                    'description' => 'Add an extra layer of security immediately.'],
                ['title' => 'Change your passwords now',
                    'description' => 'Use strong unique passwords for every account.'],
                ['title' => 'Never click suspicious links',
                    'description' => 'Always verify URLs before clicking.'],
            ],
            'medium' => [
                ['title' => 'Be cautious with email attachments',
                    'description' => 'Never open attachments from unknown senders.'],
                ['title' => 'Keep software updated',
                    'description' => 'Always install security updates.'],
            ],
            'low' => [
                ['title' => 'Stay updated on new threats',
                    'description' => 'Follow cybersecurity news regularly.'],
                ['title' => 'Help others stay safe',
                    'description' => 'Share your knowledge with friends and family.'],
            ],
        ];

        return $all[$level];
    }
}
