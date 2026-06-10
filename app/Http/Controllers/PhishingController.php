<?php

namespace App\Http\Controllers;

use App\Models\PhishingScanLog;
use App\Services\PhishingAnalyzerService;
use Illuminate\Http\Request;

class PhishingController extends Controller
{
    protected PhishingAnalyzerService $analyzer;

    public function __construct(PhishingAnalyzerService $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    public function scanEmail(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $result = $this->analyzer->analyzeEmail($request->content);

        // Save to log
        PhishingScanLog::create([
            'user_id'    => $request->user()?->id,
            'input_type' => 'email',
            'input_data' => $request->content,
            'risk_level' => $result['risk_level'],
            'risk_score' => $result['risk_score'],
            'flags'      => $result['flags'],
        ]);

        return response()->json([
            'risk_level' => $result['risk_level'],
            'risk_score' => $result['risk_score'],
            'flags'      => $result['flags'],
            'message'    => $this->getRiskMessage($result['risk_level']),
        ]);
    }

    public function history(Request $request)
    {
        $logs = PhishingScanLog::where('user_id', $request->user()->id)
                                ->latest()
                                ->get();

        return response()->json($logs);
    }

    private function getRiskMessage(string $level): string
    {
        return match($level) {
            'high'   => '⚠️ HIGH RISK: This email shows strong signs of phishing!',
            'medium' => '⚠️ MEDIUM RISK: This email has some suspicious elements.',
            'low'    => '✅ LOW RISK: This email appears to be safe.',
        };
    }
}
