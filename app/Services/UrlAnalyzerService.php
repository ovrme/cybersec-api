<?php

namespace App\Services;

class UrlAnalyzerService
{
    protected array $suspiciousTlds = [
        '.xyz', '.tk', '.ml', '.ga', '.cf', '.gq',
        '.top', '.click', '.download', '.loan',
    ];

    protected array $suspiciousKeywords = [
        'login', 'verify', 'secure', 'account',
        'update', 'confirm', 'banking', 'paypal',
        'signin', 'password', 'credential',
    ];

    protected array $knownPhishingPatterns = [
        'paypa1', 'arnazon', 'g00gle', 'micros0ft',
        'apple-id', 'secure-login', 'account-verify',
        'login-secure', 'verify-account',
    ];

    public function analyzeUrl(string $url): array
    {
        $flags = [];
        $score = 0;
        $url_lower = strtolower($url);

        // Check HTTPS
        if (!str_starts_with($url_lower, 'https://')) {
            $flags[] = 'URL does not use HTTPS (insecure)';
            $score  += 20;
        }

        // Check if IP address used instead of domain
        if (preg_match('/https?:\/\/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $url)) {
            $flags[] = 'IP address used instead of domain name';
            $score  += 40;
        }

        // Check suspicious TLDs
        foreach ($this->suspiciousTlds as $tld) {
            if (str_contains($url_lower, $tld)) {
                $flags[] = "Suspicious domain extension: \"$tld\"";
                $score  += 20;
            }
        }

        // Check suspicious keywords in URL
        foreach ($this->suspiciousKeywords as $keyword) {
            if (str_contains($url_lower, $keyword)) {
                $flags[] = "Suspicious keyword in URL: \"$keyword\"";
                $score  += 10;
            }
        }

        // Check known phishing patterns
        foreach ($this->knownPhishingPatterns as $pattern) {
            if (str_contains($url_lower, $pattern)) {
                $flags[] = "Known phishing pattern detected: \"$pattern\"";
                $score  += 40;
            }
        }

        // Check URL length (very long URLs are suspicious)
        if (strlen($url) > 100) {
            $flags[] = 'URL is unusually long';
            $score  += 10;
        }

        // Check multiple subdomains
        $host = parse_url($url, PHP_URL_HOST);
        if ($host && substr_count($host, '.') > 3) {
            $flags[] = 'Too many subdomains (suspicious)';
            $score  += 20;
        }

        // Cap at 100
        $score = min($score, 100);

        return [
            'url'        => $url,
            'risk_score' => $score,
            'risk_level' => $this->getRiskLevel($score),
            'flags'      => $flags,
            'is_https'   => str_starts_with($url_lower, 'https://'),
        ];
    }

    private function getRiskLevel(int $score): string
    {
        if ($score >= 60) return 'high';
        if ($score >= 30) return 'medium';
        return 'low';
    }
}
