<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PhishingScanLog;
use App\Models\QuizResult;
use App\Models\Report;
use App\Models\RiskScore;
use App\Models\UserLessonProgress;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = Report::where('user_id', $request->user()->id)->latest()->get();

        return view('reports.index', compact('reports'));
    }

    public function generate(Request $request)
    {
        $userId = $request->user()->id;
        $user   = $request->user();

        $riskScore = RiskScore::where('user_id', $userId)->latest()->first();

        $quizResults = QuizResult::where('user_id', $userId)->get();
        $quizSummary = [
            'total_taken'   => $quizResults->count(),
            'average_score' => $quizResults->count() ? round($quizResults->avg('percentage'), 2) : 0,
            'highest_score' => $quizResults->count() ? $quizResults->max('percentage') : 0,
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

        $level = $riskScore?->level ?? 'medium';
        $all = [
            'high' => [
                ['title' => 'Enable Two-Factor Authentication', 'description' => 'Add an extra layer of security immediately.'],
                ['title' => 'Change your passwords now', 'description' => 'Use strong unique passwords for every account.'],
                ['title' => 'Never click suspicious links', 'description' => 'Always verify URLs before clicking.'],
            ],
            'medium' => [
                ['title' => 'Be cautious with email attachments', 'description' => 'Never open attachments from unknown senders.'],
                ['title' => 'Keep software updated', 'description' => 'Always install security updates.'],
            ],
            'low' => [
                ['title' => 'Stay updated on new threats', 'description' => 'Follow cybersecurity news regularly.'],
                ['title' => 'Help others stay safe', 'description' => 'Share your knowledge with friends and family.'],
            ],
        ];
        $recommendations = $all[$level] ?? $all['medium'];

        $data = [
            'user'            => ['name' => $user->name, 'email' => $user->email],
            'risk_score'      => $riskScore,
            'quiz_summary'    => $quizSummary,
            'scan_summary'    => $scanSummary,
            'lesson_summary'  => $lessonSummary,
            'recommendations' => $recommendations,
            'generated_at'    => now()->format('Y-m-d H:i:s'),
        ];

        $pdf      = Pdf::loadView('reports.user_report', $data);
        $filename = 'report_user_' . $userId . '_' . now()->format('Ymd_His') . '.pdf';
        $path     = 'reports/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());

        Report::create([
            'user_id'     => $userId,
            'file_path'   => $path,
            'report_data' => $data,
        ]);

        return $pdf->download($filename);
    }
}
