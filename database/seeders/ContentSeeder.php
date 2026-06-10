<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fills the app with realistic demo content.
 *
 * It is SCHEMA-AWARE: before inserting, it checks which columns actually
 * exist on each table and only writes those. This means it works even if
 * your migrations name things slightly differently — no "unknown column" errors.
 *
 * Run with:  php artisan db:seed --class=ContentSeeder
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedLessons();
        $this->seedQuiz();
        $this->seedSimulations();

        $this->command->info('✓ Demo content seeded. Refresh your app.');
    }

    /** Insert a row using only columns that exist on the table. */
    private function insertSafe(string $table, array $data): ?int
    {
        if (! Schema::hasTable($table)) {
            $this->command->warn("• skipped {$table} (table not found)");
            return null;
        }

        $cols = Schema::getColumnListing($table);
        $row  = array_intersect_key($data, array_flip($cols));

        // timestamps if present
        if (in_array('created_at', $cols)) $row['created_at'] = now();
        if (in_array('updated_at', $cols)) $row['updated_at'] = now();

        return DB::table($table)->insertGetId($row);
    }

    private function seedLessons(): void
    {
        $lessons = [
            [
                'title' => 'Recognizing Phishing Emails',
                'category' => 'Email Security',
                'difficulty' => 'beginner',
                'content' => "Phishing is one of the most common cyberattacks, where attackers send fraudulent messages designed to trick you into revealing sensitive information.\n\nWatch for these warning signs:\n- A false sense of urgency (\"Your account will be closed in 24 hours!\")\n- Generic greetings like \"Dear Customer\" instead of your name\n- Sender addresses that look almost right but are slightly off (support@paypa1.com)\n- Links that don't match the company's real website when you hover over them\n- Requests for passwords, payment details, or personal information\n- Spelling and grammar mistakes\n\nIf you're unsure whether an email is real, never click its links. Go directly to the company's website by typing the address yourself.",
            ],
            [
                'title' => 'Creating Strong Passwords',
                'category' => 'Account Security',
                'difficulty' => 'beginner',
                'content' => "A strong password is your first line of defense against unauthorized access.\n\nBest practices:\n- Use at least 12 characters, mixing uppercase, lowercase, numbers and symbols\n- Avoid names, birthdays, and common dictionary words\n- Use a unique password for every account so one breach doesn't expose the rest\n- Consider a reputable password manager to generate and store them\n- Enable two-factor authentication (2FA) wherever it's offered\n\nA passphrase of random words like 'correct-horse-battery-staple' is both strong and memorable.",
            ],
            [
                'title' => 'Safe Browsing Habits',
                'category' => 'Web Security',
                'difficulty' => 'intermediate',
                'content' => "Your browser is the front door to most online threats. Keep it locked.\n\n- Always check for 'https://' and the padlock icon before entering information\n- Avoid downloading files from untrusted sources\n- Keep your browser and its extensions updated\n- Be cautious on public Wi-Fi — avoid banking or sensitive logins unless on a VPN\n- Watch for lookalike domains (g00gle.com, faceb00k.com)\n- Clear cookies and cache periodically, and review which sites have saved logins",
            ],
            [
                'title' => 'Spotting Social Engineering',
                'category' => 'Threat Awareness',
                'difficulty' => 'intermediate',
                'content' => "Social engineering manipulates people, not machines, into giving up confidential information.\n\nCommon tactics:\n- Impersonating IT staff, coworkers, or authority figures\n- Creating urgency or fear to short-circuit your judgment\n- Pretexting — inventing a believable scenario to extract data\n- Tailgating into secure physical spaces\n\nDefense: verify identities independently before sharing anything sensitive. Never feel pressured to act immediately — a real request will survive a verification call.",
            ],
            [
                'title' => 'Securing Your Mobile Devices',
                'category' => 'Device Security',
                'difficulty' => 'beginner',
                'content' => "Phones hold as much sensitive data as computers, but are easier to lose.\n\n- Use a strong PIN, password, or biometric lock\n- Enable remote-wipe / 'Find My Device'\n- Only install apps from official stores, and review their permissions\n- Keep your OS updated\n- Be wary of public USB charging ports ('juice jacking') — use a power-only cable\n- Turn off Bluetooth and Wi-Fi auto-connect when not in use",
            ],
            [
                'title' => 'Understanding Ransomware',
                'category' => 'Threat Awareness',
                'difficulty' => 'advanced',
                'content' => "Ransomware encrypts your files and demands payment for their release. It often arrives through phishing attachments or compromised downloads.\n\nProtect yourself:\n- Keep regular, offline backups — the single best defense\n- Never enable macros in documents from unknown senders\n- Keep systems patched against known exploits\n- Segment important data so one infection can't reach everything\n\nIf hit: disconnect from the network immediately, don't pay if avoidable, and report the incident. Payment funds future attacks and doesn't guarantee recovery.",
            ],
        ];

        $i = 0;
        foreach ($lessons as $l) {
            $this->insertSafe('lessons', $l + [
                'is_active'   => true,
                'order_index' => $i,
                'body'        => $l['content'], // in case column is named 'body'
                'description' => \Illuminate\Support\Str::limit(strip_tags($l['content']), 120),
            ]);
            $i++;
        }
        $this->command->info("• lessons: {$i} added");
    }

    private function seedQuiz(): void
    {
        $quizId = $this->insertSafe('quizzes', [
            'title'       => 'Phishing Awareness Challenge',
            'description' => 'Test your ability to spot phishing attempts and stay safe online.',
            'is_active'   => true,
            'difficulty'  => 'beginner',
            'category'    => 'Email Security',
        ]);

        if (! $quizId) return;

        $questions = [
            [
                'question' => 'An email says your account will be suspended in 1 hour unless you "verify" by clicking a link. What should you do?',
                'option_a' => 'Click the link quickly to avoid suspension',
                'option_b' => 'Go to the site directly by typing the address yourself',
                'option_c' => 'Reply with your password to confirm identity',
                'option_d' => 'Forward it to all your contacts',
                'correct_answer' => 'b',
                'explanation' => 'Urgency is a classic pressure tactic. Never click — navigate to the official site yourself.',
            ],
            [
                'question' => 'Which sender address is most likely a phishing attempt impersonating PayPal?',
                'option_a' => 'service@paypal.com',
                'option_b' => 'support@paypal.com',
                'option_c' => 'security@paypa1.com',
                'option_d' => 'help@paypal.com',
                'correct_answer' => 'c',
                'explanation' => 'paypa1.com uses the number "1" instead of the letter "l" — a lookalike domain.',
            ],
            [
                'question' => 'What is the safest way to check where a link actually goes before clicking?',
                'option_a' => 'Click it and see what loads',
                'option_b' => 'Hover over it to preview the real URL',
                'option_c' => 'Trust the visible text of the link',
                'option_d' => 'Copy it into a social media post',
                'correct_answer' => 'b',
                'explanation' => 'Hovering reveals the true destination, which often differs from the displayed text.',
            ],
            [
                'question' => 'A "coworker" emails urgently asking you to buy gift cards and send the codes. This is likely:',
                'option_a' => 'A normal work request',
                'option_b' => 'A business email compromise / social engineering scam',
                'option_c' => 'A reward program',
                'option_d' => 'A software update',
                'correct_answer' => 'b',
                'explanation' => 'Gift-card requests via email are a hallmark of business email compromise. Verify by phone.',
            ],
            [
                'question' => 'Which of these makes the strongest password?',
                'option_a' => 'password123',
                'option_b' => 'your pet\'s name',
                'option_c' => 'a 14-character random passphrase',
                'option_d' => 'your birthdate',
                'correct_answer' => 'c',
                'explanation' => 'Length and randomness beat everything. Personal info and common words are easily guessed.',
            ],
            [
                'question' => 'You receive an unexpected attachment named "Invoice.zip" from an unknown sender. You should:',
                'option_a' => 'Open it to see what it is',
                'option_b' => 'Enable macros if prompted',
                'option_c' => 'Delete it without opening',
                'option_d' => 'Forward it to your finance team to open',
                'correct_answer' => 'c',
                'explanation' => 'Unexpected attachments — especially archives — are a common malware delivery method.',
            ],
        ];

        $n = 0;
        foreach ($questions as $q) {
            $this->insertSafe('quiz_questions', $q + ['quiz_id' => $quizId]);
            $n++;
        }
        $this->command->info("• quiz: 1 added with {$n} questions");
    }

    private function seedSimulations(): void
    {
        $sims = [
            [
                'title' => 'The Urgent CEO Request',
                'type' => 'social_engineering',
                'difficulty' => 'beginner',
                'description' => 'A message appearing to come from your CEO asks for an urgent favor.',
                'content' => "From: Michael Chen, CEO\nSubject: Quick favor — urgent\n\nHi, I'm in back-to-back meetings and can't talk. I need you to purchase five \$100 gift cards for a client gift and send me the codes. I'll reimburse you today. Please keep this between us for now.",
                'scenario' => 'A message appearing to come from your CEO asks you to buy gift cards urgently and keep it quiet.',
            ],
            [
                'title' => 'Package Delivery Notice',
                'type' => 'phishing_email',
                'difficulty' => 'beginner',
                'description' => 'A delivery notification email asks you to confirm your address.',
                'content' => "From: delivery@dhl-tracking-verify.com\nSubject: Your package is on hold\n\nWe attempted delivery but your address could not be confirmed. Click here to verify your details within 24 hours or your package will be returned: http://dhl-verify.tracking-secure.net/confirm",
                'scenario' => 'A delivery email from a suspicious domain pressures you to "verify" your address via a link.',
            ],
            [
                'title' => 'Bank Security Alert',
                'type' => 'fake_url',
                'difficulty' => 'intermediate',
                'description' => 'A text message warns of suspicious activity on your bank account.',
                'content' => "SMS: [YourBank] Suspicious login detected from a new device. If this wasn't you, secure your account now: http://yourbank.secure-login-alert.com",
                'scenario' => 'A text claims suspicious activity and links to a lookalike banking domain.',
            ],
            [
                'title' => 'IT Password Reset',
                'type' => 'social_engineering',
                'difficulty' => 'intermediate',
                'description' => 'Someone claiming to be from IT calls asking for your password.',
                'content' => "Phone call: \"Hi, this is Dave from IT support. We're migrating email accounts tonight and I need to confirm your current password so we don't lock you out. Can you read it to me?\"",
                'scenario' => 'A caller claiming to be IT asks you to read out your current password.',
            ],
        ];

        $n = 0;
        foreach ($sims as $s) {
            $this->insertSafe('attack_simulations', $s + ['is_active' => true]);
            $n++;
        }
        $this->command->info("• simulations: {$n} added");
    }
}
