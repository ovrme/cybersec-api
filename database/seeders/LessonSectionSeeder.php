<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\LessonSection;
use Illuminate\Database\Seeder;

/**
 * Builds the interactive 4-part version of "Recognizing Phishing Emails"
 * (read → cards → exercise → quiz) and fills duration/points defaults.
 *
 * Safe to re-run: it replaces that lesson's sections each time.
 *
 * Run with:  php artisan db:seed --class=LessonSectionSeeder
 */
class LessonSectionSeeder extends Seeder
{
    public function run(): void
    {
        // Sensible defaults so cards can show time + points.
        Lesson::whereNull('duration_minutes')->update(['duration_minutes' => 8]);
        Lesson::whereNull('points')->update(['points' => 50]);

        $lesson = Lesson::where('title', 'like', '%Phishing Email%')->first();
        if (! $lesson) {
            $this->command->warn('• "Recognizing Phishing Emails" lesson not found — skipped.');
            return;
        }

        $lesson->update([
            'summary'          => 'Learn the four red flags that give away almost every phishing email — then prove it on a real specimen.',
            'duration_minutes' => 8,
            'points'           => 50,
        ]);

        LessonSection::where('lesson_id', $lesson->id)->delete();

        // ---- Part 1 · Read ----
        LessonSection::create([
            'lesson_id'  => $lesson->id,
            'sort_order' => 1,
            'type'       => 'read',
            'label'      => 'Read',
            'title'      => 'Why phishing works',
            'body'       => '<p>Over 90% of successful breaches start with a phishing email. Not because the technology is clever — but because the email is designed to make you act before you think.</p>'
                          . '<p>Phishing emails borrow trust (a brand you know), add pressure (a deadline, a threat, a reward), and give you one easy action: click. Your best defence isn\'t software. It\'s a 10-second pause and knowing exactly what to look for.</p>'
                          . '<p>In this lesson you\'ll learn the four red flags that appear in almost every phishing attempt, then practise finding them in a real example.</p>',
        ]);

        // ---- Part 2 · Cards ----
        LessonSection::create([
            'lesson_id'  => $lesson->id,
            'sort_order' => 2,
            'type'       => 'cards',
            'label'      => 'Learn',
            'title'      => 'The four red flags',
            'body'       => '<p>Open each card. You need to view all four before continuing.</p>',
            'meta'       => [
                'cards' => [
                    [
                        'icon'    => '🎭',
                        'title'   => "Sender doesn't match",
                        'body'    => 'The display name says one thing, the actual address says another. Look past the name — read the domain after the @.',
                        'example' => '"PayPal" <security@paypa1-support.com>',
                    ],
                    [
                        'icon'    => '⏱',
                        'title'   => 'Artificial urgency',
                        'body'    => 'Deadlines, threats of suspension, "act now". Real companies rarely demand action within hours — attackers do, so you skip the pause.',
                        'example' => '"…within 24 hours or your account will be closed"',
                    ],
                    [
                        'icon'    => '🔗',
                        'title'   => 'Link goes somewhere else',
                        'body'    => 'The button says one thing; the real URL points elsewhere. Hover (or long-press on mobile) before you ever click.',
                        'example' => 'http://paypa1-secure.win/login',
                    ],
                    [
                        'icon'    => '👤',
                        'title'   => 'Generic greeting',
                        'body'    => '"Dear Customer", "Dear User". A real service that holds your account knows your name. Mass attacks don\'t.',
                        'example' => '"Dear Valued Customer,"',
                    ],
                ],
            ],
        ]);

        // ---- Part 3 · Exercise ----
        LessonSection::create([
            'lesson_id'  => $lesson->id,
            'sort_order' => 3,
            'type'       => 'exercise',
            'label'      => 'Practise',
            'title'      => 'Hands-on: spot the red flags',
            'body'       => '<p>This email was reported by a real user (details changed). Click anything that looks suspicious. There are <strong>4 red flags</strong> hiding in it.</p>',
            'meta'       => [
                'specimen'   => '#1042',
                'from_name'  => 'PayPal Security',
                'from_email' => 'security@paypa1-support.com',
                'from_flag'  => [
                    'tag' => 'Spoofed sender',
                    'why' => 'The display name says PayPal, but the domain is paypa1-support.com — not paypal.com.',
                ],
                'to'      => 'you@example.com',
                'subject' => 'Unusual activity — verification required',
                'body'    => [
                    [
                        'hot' => 'Dear Valued Customer,',
                        'tag' => 'Generic greeting',
                        'why' => 'PayPal knows your name. "Dear Valued Customer" means a mass-mailed attack.',
                    ],
                    [
                        'text' => 'We have detected unusual login activity on your account from an unrecognised device. For your protection, some features have been temporarily limited.',
                    ],
                    [
                        'pre'  => 'To restore full access, please verify your identity ',
                        'hot'  => 'within 24 hours or your account will be permanently suspended',
                        'post' => '.',
                        'tag'  => 'Artificial urgency',
                        'why'  => 'A hard deadline plus a threat. Designed to make you click before you think.',
                    ],
                    [
                        'button' => 'Verify My Account',
                        'url'    => 'http://paypa1-secure.win/login',
                        'tag'    => 'Mismatched link',
                        'why'    => 'The button claims PayPal, but the URL is paypa1-secure.win — a look-alike domain with a "1" instead of an "l".',
                    ],
                    [
                        'text' => "Thank you for helping us keep your account secure.\nThe PayPal Security Team",
                    ],
                ],
            ],
        ]);

        // ---- Part 4 · Quiz ----
        LessonSection::create([
            'lesson_id'  => $lesson->id,
            'sort_order' => 4,
            'type'       => 'quiz',
            'label'      => 'Check',
            'title'      => 'Knowledge check',
            'body'       => '<p>Three quick questions. Instant feedback, no retries — choose carefully.</p>',
            'meta'       => [
                'questions' => [
                    [
                        'q'       => 'An email from "Microsoft Support" <help@micros0ft-team.net> asks you to reset your password. What\'s the strongest signal it\'s fake?',
                        'options' => [
                            'It mentions passwords, which is always suspicious',
                            "The sender's domain isn't a real Microsoft domain",
                            'It arrived outside business hours',
                        ],
                        'answer'  => 1,
                        'note'    => 'The domain after the @ is the truth — display names can say anything.',
                    ],
                    [
                        'q'       => 'Before clicking a button in an email, the safest habit is to:',
                        'options' => [
                            'Click it and check whether the page looks legitimate',
                            "Reply to the sender and ask if it's genuine",
                            'Hover or long-press to preview the real URL first',
                        ],
                        'answer'  => 2,
                        'note'    => 'Previewing the real URL costs two seconds and defeats most link attacks.',
                    ],
                    [
                        'q'       => '"Your account will be deleted in 12 hours unless you act now." Why do attackers add deadlines like this?',
                        'options' => [
                            'Pressure makes people act before they inspect the email',
                            'It makes the email pass spam filters more easily',
                            'Companies are legally required to state deadlines',
                        ],
                        'answer'  => 0,
                        'note'    => 'Urgency short-circuits the pause where you would normally notice the other red flags.',
                    ],
                ],
            ],
        ]);

        $this->command->info('✓ Interactive sections seeded for "' . $lesson->title . '".');
    }
}
