<?php

namespace App\Http\Controllers;

use App\Models\RiskScore;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    protected array $recommendations = [
        'high' => [
            [
                'title'       => 'Never click suspicious links',
                'description' => 'Always verify URLs before clicking. Hover over links to see the actual destination.',
                'priority'    => 'critical',
            ],
            [
                'title'       => 'Enable Two-Factor Authentication',
                'description' => 'Add an extra layer of security to all your accounts immediately.',
                'priority'    => 'critical',
            ],
            [
                'title'       => 'Change your passwords now',
                'description' => 'Use strong unique passwords for every account. Use a password manager.',
                'priority'    => 'critical',
            ],
            [
                'title'       => 'Never share personal info via email',
                'description' => 'Legitimate organizations never ask for passwords or SSN via email.',
                'priority'    => 'high',
            ],
            [
                'title'       => 'Complete all cybersecurity lessons',
                'description' => 'Your knowledge level is low. Complete all available lessons immediately.',
                'priority'    => 'high',
            ],
        ],
        'medium' => [
            [
                'title'       => 'Be cautious with email attachments',
                'description' => 'Never open attachments from unknown senders.',
                'priority'    => 'medium',
            ],
            [
                'title'       => 'Verify sender email addresses',
                'description' => 'Check the full email address, not just the display name.',
                'priority'    => 'medium',
            ],
            [
                'title'       => 'Keep software updated',
                'description' => 'Always install security updates for your OS and applications.',
                'priority'    => 'medium',
            ],
            [
                'title'       => 'Use a VPN on public WiFi',
                'description' => 'Never access sensitive accounts on public networks without a VPN.',
                'priority'    => 'medium',
            ],
            [
                'title'       => 'Take more quizzes',
                'description' => 'Test your knowledge regularly to identify weak areas.',
                'priority'    => 'low',
            ],
        ],
        'low' => [
            [
                'title'       => 'Stay updated on new threats',
                'description' => 'Follow cybersecurity news to stay aware of new phishing techniques.',
                'priority'    => 'low',
            ],
            [
                'title'       => 'Help others stay safe',
                'description' => 'Share your knowledge with friends and family about phishing.',
                'priority'    => 'low',
            ],
            [
                'title'       => 'Review privacy settings',
                'description' => 'Regularly audit your social media and app privacy settings.',
                'priority'    => 'low',
            ],
            [
                'title'       => 'Use encrypted messaging apps',
                'description' => 'Use Signal or WhatsApp for sensitive communications.',
                'priority'    => 'low',
            ],
        ],
    ];

    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Get latest risk score
        $riskScore = RiskScore::where('user_id', $userId)
                                ->latest()
                                ->first();

        // Default to medium if no score yet
        $level = $riskScore ? $riskScore->level : 'medium';

        // Get recommendations for this level
        // Also include lower priority recommendations
        $tips = $this->recommendations[$level];

        if ($level === 'high') {
            // High risk gets all recommendations
            $tips = array_merge(
                $this->recommendations['high'],
                $this->recommendations['medium']
            );
        }

        return response()->json([
            'risk_level'      => $level,
            'risk_score'      => $riskScore?->score ?? 'N/A',
            'total_tips'      => count($tips),
            'recommendations' => $tips,
            'message'         => $this->getMessage($level),
        ]);
    }

    private function getMessage(string $level): string
    {
        return match($level) {
            'high'   => '🔴 You need to take immediate action to improve your security!',
            'medium' => '🟡 You are moderately secure. Follow these tips to improve.',
            'low'    => '🟢 Great job! Here are some tips to maintain your security.',
        };
    }
}
