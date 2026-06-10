<?php

namespace App\Services;

class PhishingAnalyzerService
{
    // Suspicious keywords to detect
    protected array $suspiciousKeywords = [
        'verify your account',
        'click here immediately',
        'your account will be suspended',
        'confirm your password',
        'unusual activity',
        'winner',
        'congratulations',
        'urgent action required',
        'limited time offer',
        'act now',
        'free gift',
        'you have been selected',
        'bank account',
        'social security',
        'update your payment',
        'login to verify',
    ];

    // Suspicious sender patterns
    protected array $suspiciousDomains = [
        'gmail.co',
        'paypa1.com',
        'arnazon.com',
        'micros0ft.com',
        'secure-login',
        'account-verify',
        'update-info',
    ];

    public function analyzeEmail(string $content): array
    {
        $flags     = [];
        $score     = 0;
        $content_lower = strtolower($content);

        // Check suspicious keywords
        foreach ($this->suspiciousKeywords as $keyword) {
            if (str_contains($content_lower, strtolower($keyword))) {
                $flags[] = "Suspicious keyword: \"$keyword\"";
                $score  += 15;
            }
        }

        // Check for urgency patterns
        if (preg_match('/\b(urgent|immediately|asap|right now|today only)\b/i', $content)) {
            $flags[] = 'Urgency language detected';
            $score  += 20;
        }

        // Check for suspicious links
        if (preg_match('/https?:\/\/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $content)) {
            $flags[] = 'IP address used as link (suspicious)';
            $score  += 30;
        }

        // Check for misspelled domains
        foreach ($this->suspiciousDomains as $domain) {
            if (str_contains($content_lower, $domain)) {
                $flags[] = "Suspicious domain pattern: \"$domain\"";
                $score  += 25;
            }
        }

        // Cap score at 100
        $score = min($score, 100);

        return [
            'risk_score' => $score,
            'risk_level' => $this->getRiskLevel($score),
            'flags'      => $flags,
        ];
    }

    private function getRiskLevel(int $score): string
    {
        if ($score >= 60) return 'high';
        if ($score >= 30) return 'medium';
        return 'low';
    }
}
