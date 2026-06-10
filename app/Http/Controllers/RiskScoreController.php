<?php

namespace App\Http\Controllers;

use App\Models\RiskScore;
use App\Services\RiskScoreService;
use Illuminate\Http\Request;

class RiskScoreController extends Controller
{
    protected RiskScoreService $service;

    public function __construct(RiskScoreService $service)
    {
        $this->service = $service;
    }

    // Generate and save risk score
    public function generate(Request $request)
    {
        $userId = $request->user()->id;
        $result = $this->service->calculate($userId);

        $riskScore = RiskScore::create([
            'user_id'         => $userId,
            'score'           => $result['score'],
            'level'           => $result['level'],
            'quiz_weight'     => $result['quiz_weight'],
            'behavior_weight' => $result['behavior_weight'],
        ]);

        return response()->json([
            'message'         => 'Risk score generated successfully',
            'score'           => $result['score'],
            'level'           => $result['level'],
            'quiz_weight'     => $result['quiz_weight'],
            'behavior_weight' => $result['behavior_weight'],
            'summary'         => $this->getSummary($result['level']),
        ]);
    }

    // Get latest risk score
    public function latest(Request $request)
    {
        $score = RiskScore::where('user_id', $request->user()->id)
                            ->latest()
                            ->first();

        if (!$score) {
            return response()->json([
                'message' => 'No risk score yet. Please generate one.',
            ], 404);
        }

        return response()->json($score);
    }

    // Get risk score history
    public function history(Request $request)
    {
        $scores = RiskScore::where('user_id', $request->user()->id)
                            ->latest()
                            ->get();

        return response()->json($scores);
    }

    private function getSummary(string $level): string
    {
        return match($level) {
            'high'   => '🔴 HIGH RISK: You are highly vulnerable. Take immediate action!',
            'medium' => '🟡 MEDIUM RISK: You have some vulnerabilities. Keep learning!',
            'low'    => '🟢 LOW RISK: Great job! You are well protected.',
        };
    }
}
