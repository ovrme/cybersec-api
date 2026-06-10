<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        // Quiz 1 — Phishing Basics
        $quiz1 = Quiz::create([
            'title'       => 'Phishing Awareness Quiz',
            'description' => 'Test your knowledge about phishing attacks and how to spot them.',
            'is_active'   => true,
        ]);

        $quiz1->questions()->createMany([
            [
                'question'       => 'What is phishing?',
                'option_a'       => 'A type of fishing sport',
                'option_b'       => 'A cyber attack that tricks you into giving personal information',
                'option_c'       => 'A software that protects your computer',
                'option_d'       => 'A method to speed up your internet',
                'correct_answer' => 'b',
                'explanation'    => 'Phishing is a cyber attack where criminals pretend to be trusted organizations to steal your personal information.',
            ],
            [
                'question'       => 'Which of these is a sign of a phishing email?',
                'option_a'       => 'It comes from your boss\'s official email',
                'option_b'       => 'It has your full name and correct details',
                'option_c'       => 'It creates urgency saying your account will be suspended',
                'option_d'       => 'It contains a professional signature',
                'correct_answer' => 'c',
                'explanation'    => 'Urgency is a major phishing tactic. Attackers want you to act fast without thinking.',
            ],
            [
                'question'       => 'You receive an email from "support@paypa1.com" asking you to verify your account. What should you do?',
                'option_a'       => 'Click the link and verify immediately',
                'option_b'       => 'Reply with your password',
                'option_c'       => 'Delete the email — it is a phishing attempt',
                'option_d'       => 'Forward it to your friends',
                'correct_answer' => 'c',
                'explanation'    => 'paypa1.com (with the number 1 instead of L) is a fake domain. This is a classic phishing attempt.',
            ],
            [
                'question'       => 'What is the safest way to visit your bank website?',
                'option_a'       => 'Click the link in an email from your bank',
                'option_b'       => 'Type the URL directly into your browser',
                'option_c'       => 'Search for it on Google and click the first result',
                'option_d'       => 'Ask a friend to send you the link',
                'correct_answer' => 'b',
                'explanation'    => 'Always type the URL directly. Links in emails can redirect you to fake websites.',
            ],
            [
                'question'       => 'A legitimate company will ask for your password via email.',
                'option_a'       => 'True — they need it to verify your identity',
                'option_b'       => 'False — legitimate companies never ask for passwords via email',
                'option_c'       => 'Sometimes — only if you forgot it',
                'option_d'       => 'Only banks do this',
                'correct_answer' => 'b',
                'explanation'    => 'No legitimate company will ever ask for your password via email. This is always a red flag.',
            ],
        ]);

        // Quiz 2 — Password Security
        $quiz2 = Quiz::create([
            'title'       => 'Password Security Quiz',
            'description' => 'How well do you know password best practices?',
            'is_active'   => true,
        ]);

        $quiz2->questions()->createMany([
            [
                'question'       => 'Which of these is the strongest password?',
                'option_a'       => 'password123',
                'option_b'       => 'John1990',
                'option_c'       => 'qwerty',
                'option_d'       => 'Xk#9mP!2vL$nQ7',
                'correct_answer' => 'd',
                'explanation'    => 'A strong password is long, random, and contains a mix of letters, numbers, and symbols.',
            ],
            [
                'question'       => 'What should you do if a website gets hacked and your password is leaked?',
                'option_a'       => 'Wait and see what happens',
                'option_b'       => 'Change your password on that site only',
                'option_c'       => 'Change your password on that site AND all sites where you used the same password',
                'option_d'       => 'Delete your account',
                'correct_answer' => 'c',
                'explanation'    => 'If you reuse passwords, a breach on one site puts all your other accounts at risk.',
            ],
            [
                'question'       => 'What is a password manager?',
                'option_a'       => 'A person who manages passwords for a company',
                'option_b'       => 'A software that securely stores and generates strong passwords',
                'option_c'       => 'A notebook where you write down passwords',
                'option_d'       => 'A feature that shows your saved browser passwords',
                'correct_answer' => 'b',
                'explanation'    => 'Password managers securely store all your passwords so you only need to remember one master password.',
            ],
            [
                'question'       => 'How often should you change your passwords?',
                'option_a'       => 'Every day',
                'option_b'       => 'Never — once set it stays strong',
                'option_c'       => 'Only when you suspect a breach or every 6-12 months',
                'option_d'       => 'Every week',
                'correct_answer' => 'c',
                'explanation'    => 'Change passwords when you suspect a breach, or periodically every 6-12 months for important accounts.',
            ],
            [
                'question'       => 'Is it safe to use the same password for multiple accounts?',
                'option_a'       => 'Yes, it is easier to remember',
                'option_b'       => 'No — if one account is breached, all accounts are at risk',
                'option_c'       => 'Only if the password is very strong',
                'option_d'       => 'Yes, as long as you use 2FA',
                'correct_answer' => 'b',
                'explanation'    => 'Using the same password everywhere means one breach exposes all your accounts. Always use unique passwords.',
            ],
        ]);

        // Quiz 3 — General Cybersecurity
        $quiz3 = Quiz::create([
            'title'       => 'General Cybersecurity Knowledge',
            'description' => 'A broad test covering common cybersecurity concepts everyone should know.',
            'is_active'   => true,
        ]);

        $quiz3->questions()->createMany([
            [
                'question'       => 'What does 2FA stand for?',
                'option_a'       => 'Two File Access',
                'option_b'       => 'Two-Factor Authentication',
                'option_c'       => 'Two Firewall Applications',
                'option_d'       => 'Total Fraud Alert',
                'correct_answer' => 'b',
                'explanation'    => '2FA stands for Two-Factor Authentication — it adds a second layer of security to your login.',
            ],
            [
                'question'       => 'What should you do before connecting to public WiFi?',
                'option_a'       => 'Nothing — public WiFi is safe',
                'option_b'       => 'Enable a VPN',
                'option_c'       => 'Log into your bank account quickly',
                'option_d'       => 'Share the password with friends',
                'correct_answer' => 'b',
                'explanation'    => 'A VPN encrypts your traffic on public WiFi, protecting you from attackers on the same network.',
            ],
            [
                'question'       => 'What is ransomware?',
                'option_a'       => 'Software that speeds up your computer',
                'option_b'       => 'Malware that encrypts your files and demands payment',
                'option_c'       => 'A type of antivirus program',
                'option_d'       => 'A secure way to send files',
                'correct_answer' => 'b',
                'explanation'    => 'Ransomware encrypts your files and demands a ransom payment to unlock them, often in cryptocurrency.',
            ],
            [
                'question'       => 'A stranger calls claiming to be from Microsoft saying your computer has a virus. What should you do?',
                'option_a'       => 'Follow their instructions immediately',
                'option_b'       => 'Give them remote access to fix it',
                'option_c'       => 'Hang up — this is a tech support scam',
                'option_d'       => 'Pay the fee they ask for',
                'correct_answer' => 'c',
                'explanation'    => 'Microsoft never calls you unsolicited. This is a classic tech support scam designed to steal money or access.',
            ],
            [
                'question'       => 'What does HTTPS mean in a website URL?',
                'option_a'       => 'The website is fast',
                'option_b'       => 'The website is popular',
                'option_c'       => 'The connection between you and the website is encrypted',
                'option_d'       => 'The website is owned by a government',
                'correct_answer' => 'c',
                'explanation'    => 'HTTPS means the connection is encrypted using SSL/TLS, protecting data in transit from eavesdroppers.',
            ],
            [
                'question'       => 'Which of these is the safest action when you receive an unexpected email attachment?',
                'option_a'       => 'Open it to see what it is',
                'option_b'       => 'Forward it to colleagues',
                'option_c'       => 'Do not open it — verify with the sender first through a separate channel',
                'option_d'       => 'Save it for later',
                'correct_answer' => 'c',
                'explanation'    => 'Never open unexpected attachments. Contact the sender through a separate channel to verify before opening.',
            ],
        ]);

        // Quiz 4 — URL Safety
        $quiz4 = Quiz::create([
            'title'       => 'URL & Website Safety Quiz',
            'description' => 'Can you spot a fake or dangerous URL? Test your skills here.',
            'is_active'   => true,
        ]);

        $quiz4->questions()->createMany([
            [
                'question'       => 'Which URL is most likely fake?',
                'option_a'       => 'https://www.amazon.com',
                'option_b'       => 'https://www.arnazon.com/login',
                'option_c'       => 'https://www.google.com',
                'option_d'       => 'https://www.paypal.com',
                'correct_answer' => 'b',
                'explanation'    => 'arnazon.com is a typosquatted domain designed to look like amazon.com — a classic phishing trick.',
            ],
            [
                'question'       => 'What does a padlock icon in the browser address bar mean?',
                'option_a'       => 'The website is safe and trustworthy',
                'option_b'       => 'The connection is encrypted',
                'option_c'       => 'The website is owned by a verified company',
                'option_d'       => 'The website has no viruses',
                'correct_answer' => 'b',
                'explanation'    => 'The padlock only means the connection is encrypted (HTTPS). It does NOT mean the website is legitimate or safe.',
            ],
            [
                'question'       => 'Which domain extension is most commonly associated with suspicious websites?',
                'option_a'       => '.com',
                'option_b'       => '.gov',
                'option_c'       => '.org',
                'option_d'       => '.tk',
                'correct_answer' => 'd',
                'explanation'    => '.tk (and .ml, .ga, .cf) are free domain extensions frequently used by phishers and scammers.',
            ],
            [
                'question'       => 'You want to check if a URL is safe before clicking it. What tool should you use?',
                'option_a'       => 'Google Translate',
                'option_b'       => 'VirusTotal.com',
                'option_c'       => 'YouTube',
                'option_d'       => 'Wikipedia',
                'correct_answer' => 'b',
                'explanation'    => 'VirusTotal.com scans URLs and files against dozens of antivirus engines to check for malware.',
            ],
        ]);
    }
}
