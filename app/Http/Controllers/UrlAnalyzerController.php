<?php

namespace App\Http\Controllers;

use App\Models\PhishingScanLog;
use App\Services\UrlAnalyzerService;
use Illuminate\Http\Request;

class UrlAnalyzerController extends Controller
{
    protected UrlAnalyzerService $analyzer;

    public function __construct(UrlAnalyzerService $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'url' => 'required|string',
        ]);

        $result = $this->analyzer->analyzeUrl($request->url);

        // Save to phishing scan logs
        PhishingScanLog::create([
            'user_id'    => $request->user()?->id,
            'input_type' => 'url',
            'input_data' => $request->url,
            'risk_level' => $result['risk_level'],
            'risk_score' => $result['risk_score'],
            'flags'      => $result['flags'],
        ]);

        return response()->json([
            'url'        => $result['url'],
            'is_https'   => $result['is_https'],
            'risk_level' => $result['risk_level'],
            'risk_score' => $result['risk_score'],
            'flags'      => $result['flags'],
            'message'    => $this->getRiskMessage($result['risk_level']),
        ]);
    }

    private function getRiskMessage(string $level): string
    {
        return match($level) {
            'high'   => '🚨 HIGH RISK: This URL is likely malicious!',
            'medium' => '⚠️ MEDIUM RISK: This URL has suspicious elements.',
            'low'    => '✅ LOW RISK: This URL appears to be safe.',
        };
    }
}
