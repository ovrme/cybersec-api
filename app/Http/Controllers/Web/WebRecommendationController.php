<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RiskScore;
use Illuminate\Http\Request;

class WebRecommendationController extends Controller
{
    protected array $recommendations = [
        'high' => [
            ['title' => 'Never click suspicious links', 'description' => 'Hover to preview the real destination before clicking anything.', 'priority' => 'critical'],
            ['title' => 'Enable Two-Factor Authentication', 'description' => 'Add a second layer of security to every important account today.', 'priority' => 'critical'],
            ['title' => 'Change your passwords now', 'description' => 'Use long, unique passwords and a password manager to store them.', 'priority' => 'critical'],
            ['title' => 'Never share personal info via email', 'description' => 'Legitimate organizations never ask for passwords or SSNs by email.', 'priority' => 'high'],
            ['title' => 'Complete the learning modules', 'description' => 'Your awareness score is low — work through every lesson.', 'priority' => 'high'],
        ],
        'medium' => [
            ['title' => 'Be cautious with attachments', 'description' => 'Never open attachments from senders you do not recognize.', 'priority' => 'medium'],
            ['title' => 'Verify sender addresses', 'description' => 'Check the full address, not just the friendly display name.', 'priority' => 'medium'],
            ['title' => 'Keep software updated', 'description' => 'Install security updates for your OS and apps promptly.', 'priority' => 'medium'],
            ['title' => 'Use a VPN on public Wi-Fi', 'description' => 'Never access sensitive accounts on open networks unprotected.', 'priority' => 'medium'],
            ['title' => 'Take more quizzes', 'description' => 'Test yourself regularly to find and fix weak spots.', 'priority' => 'low'],
        ],
        'low' => [
            ['title' => 'Stay current on threats', 'description' => 'Follow security news to learn new phishing techniques.', 'priority' => 'low'],
            ['title' => 'Help others stay safe', 'description' => 'Share what you have learned with friends and family.', 'priority' => 'low'],
            ['title' => 'Review privacy settings', 'description' => 'Audit your social and app privacy settings periodically.', 'priority' => 'low'],
            ['title' => 'Use encrypted messaging', 'description' => 'Prefer Signal or WhatsApp for sensitive conversations.', 'priority' => 'low'],
        ],
    ];

    public function index(Request $request)
    {
        $risk  = RiskScore::where('user_id', $request->user()->id)->latest()->first();
        $level = $risk?->level ?? 'medium';

        $tips = $level === 'high'
            ? array_merge($this->recommendations['high'], $this->recommendations['medium'])
            : $this->recommendations[$level];

        return view('recommendations.index', [
            'tips'  => $tips,
            'level' => $level,
            'score' => $risk?->score,
        ]);
    }
}
