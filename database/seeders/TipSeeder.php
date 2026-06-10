<?php

namespace Database\Seeders;

use App\Models\Tip;
use Illuminate\Database\Seeder;

/**
 * Ports the previously hardcoded recommendations into the tips table.
 * Safe to re-run: matches on title.
 *
 * Run with:  php artisan db:seed --class=TipSeeder
 */
class TipSeeder extends Seeder
{
    public function run(): void
    {
        $tips = [
            // ---- Shown to HIGH-risk users ----
            ['high', 'critical', 'Never click suspicious links', 'Hover to preview the real destination before clicking anything.'],
            ['high', 'critical', 'Enable Two-Factor Authentication', 'Add a second layer of security to every important account today.'],
            ['high', 'critical', 'Change your passwords now', 'Use long, unique passwords and a password manager to store them.'],
            ['high', 'high', 'Never share personal info via email', 'Legitimate organizations never ask for passwords or codes by email.'],
            ['high', 'high', 'Complete the learning modules', 'Your awareness score is low — work through every lesson.'],

            // ---- Shown to MEDIUM-risk users (and high-risk users too) ----
            ['medium', 'medium', 'Be cautious with attachments', 'Never open attachments from senders you do not recognize.'],
            ['medium', 'medium', 'Verify sender addresses', 'Check the full address, not just the friendly display name.'],
            ['medium', 'medium', 'Keep software updated', 'Install security updates for your OS and apps promptly.'],
            ['medium', 'medium', 'Use a VPN on public Wi-Fi', 'Never access sensitive accounts on open networks unprotected.'],
            ['medium', 'low', 'Take more quizzes', 'Test yourself regularly to find and fix weak spots.'],

            // ---- Shown to LOW-risk users ----
            ['low', 'low', 'Stay current on threats', 'Follow security news to learn new phishing techniques.'],
            ['low', 'low', 'Help others stay safe', 'Share what you have learned with friends and family.'],
            ['low', 'low', 'Review privacy settings', 'Audit your social and app privacy settings periodically.'],
            ['low', 'low', 'Use encrypted messaging', 'Prefer Signal or WhatsApp for sensitive conversations.'],
        ];

        foreach ($tips as [$level, $priority, $title, $description]) {
            Tip::updateOrCreate(
                ['title' => $title],
                compact('level', 'priority', 'description') + ['is_active' => true],
            );
        }

        $this->command->info('✓ ' . count($tips) . ' tips seeded.');
    }
}
