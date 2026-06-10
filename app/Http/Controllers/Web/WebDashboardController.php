<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\PhishingScanLog;
use App\Models\Quiz;
use App\Models\QuizResult;
use App\Models\RiskScore;
use App\Models\User;
use App\Models\UserLessonProgress;
use App\Services\RiskScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WebDashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $risk        = RiskScore::where('user_id', $userId)->latest()->first();
        $quizResults = QuizResult::where('user_id', $userId)->get();
        $scanLogs    = PhishingScanLog::where('user_id', $userId)->get();
        $progress    = UserLessonProgress::where('user_id', $userId)->get();
        $recentScans = PhishingScanLog::where('user_id', $userId)->latest()->take(5)->get();

        $quizCount    = $quizResults->count();
        $quizAvg      = $quizCount ? round($quizResults->avg('percentage'), 1) : 0;
        $scanCount    = $scanLogs->count();
        $highRiskScans= $scanLogs->where('risk_level', 'high')->count();
        $completedIds = $progress->where('is_completed', true)->pluck('lesson_id')->all();
        $lessonsDone  = count($completedIds);
        $totalLessons = Lesson::where('is_active', true)->count();

        // ---- Score trend ----
        $riskHistory = RiskScore::where('user_id', $userId)->orderBy('created_at')->take(8)->pluck('score')->all();
        $prevScore   = count($riskHistory) >= 2 ? $riskHistory[count($riskHistory) - 2] : null;
        $scoreDelta  = ($risk && $prevScore !== null) ? $risk->score - $prevScore : null;

        // ---- Score breakdown bars ----
        // quiz performance = avg quiz %, lessons = % of lessons done,
        // safe behaviour = inverse of how risky the user's scans have been.
        $behaviour = $scanCount
            ? (int) round(100 - ($scanLogs->avg('risk_score') ?? 0))
            : 100;
        $behaviour = max(0, min(100, $behaviour));
        $breakdown = [
            ['label' => 'Quiz performance',  'pct' => (int) round($quizAvg),                                  'color' => 'teal'],
            ['label' => 'Safe behaviour',    'pct' => $behaviour,                                             'color' => 'amber'],
            ['label' => 'Lessons completed', 'pct' => $totalLessons ? (int) round($lessonsDone / $totalLessons * 100) : 0, 'color' => 'teal'],
        ];

        // ---- Recent quizzes ----
        $quizTitles = Quiz::pluck('title', 'id');
        $recentQuizzes = QuizResult::where('user_id', $userId)->latest()->take(5)->get()
            ->map(fn ($r) => [
                'title'      => $quizTitles[$r->quiz_id] ?? 'Quiz',
                'percentage' => (float) $r->percentage,
                'score'      => $r->score,
                'total'      => $r->total,
                'when'       => optional($r->created_at)->diffForHumans() ?? '',
            ]);

        // ---- Continue learning (lesson in progress, else next) + next lesson ----
        $nextLesson = Lesson::where('is_active', true)
            ->when($completedIds, fn ($q) => $q->whereNotIn('id', $completedIds))
            ->orderBy('order_index')->first();

        // ---- Leaderboard rank ----
        $rows = User::where('role', 'user')->get()->map(function (User $u) {
            $avg     = QuizResult::where('user_id', $u->id)->avg('percentage');
            $taken   = QuizResult::where('user_id', $u->id)->count();
            $lessons = UserLessonProgress::where('user_id', $u->id)->where('is_completed', true)->count();
            return ['id' => $u->id, 'taken' => $taken, 'points' => round(($avg ?? 0) + $lessons * 2, 1)];
        })->filter(fn ($r) => $r['taken'] > 0)->sortByDesc('points')->values();
        $totalRanked = $rows->count();
        $rank = null;
        foreach ($rows as $i => $r) { if ($r['id'] === $userId) { $rank = $i + 1; break; } }

        // ---- Weekly activity ----
        $week = [];
        for ($d = 6; $d >= 0; $d--) {
            $day = Carbon::today()->subDays($d);
            $week[] = [
                'label' => $day->format('D'),
                'count' => QuizResult::where('user_id', $userId)->whereDate('created_at', $day)->count()
                         + PhishingScanLog::where('user_id', $userId)->whereDate('created_at', $day)->count(),
            ];
        }
        $weekMax    = max(1, collect($week)->max('count'));
        $weekTotal  = collect($week)->sum('count');

        // ---- Day streak (consecutive days with activity, counting back from today) ----
        $streak = 0;
        for ($d = 0; $d < 60; $d++) {
            $day = Carbon::today()->subDays($d);
            $active = QuizResult::where('user_id', $userId)->whereDate('created_at', $day)->exists()
                   || PhishingScanLog::where('user_id', $userId)->whereDate('created_at', $day)->exists();
            if ($active) { $streak++; } elseif ($d > 0) { break; } else { /* today inactive, keep checking */ }
        }

        // ---- Achievements (computed on the fly, no new tables) ----
        $bestQuiz = $quizCount ? $quizResults->max('percentage') : 0;
        $achievements = [
            ['name' => 'Phishing Pro',   'desc' => 'Scored 90%+ on a quiz',     'earned' => $bestQuiz >= 90],
            ['name' => 'Quick Learner',  'desc' => 'Completed 5+ lessons',      'earned' => $lessonsDone >= 5],
            ['name' => 'Streak Starter', 'desc' => '3-day activity streak',     'earned' => $streak >= 3],
            ['name' => 'Sharp Eye',      'desc' => 'Ran 10+ scans',             'earned' => $scanCount >= 10],
        ];

        // ---- Continue-learning progress (use next lesson's category as the "track") ----
        $continue = null;
        if ($nextLesson && $nextLesson->category) {
            $catLessons = Lesson::where('is_active', true)->where('category', $nextLesson->category)->pluck('id')->all();
            $catDone    = count(array_intersect($catLessons, $completedIds));
            $continue = [
                'title' => $nextLesson->title,
                'id'    => $nextLesson->id,
                'done'  => $catDone,
                'total' => count($catLessons),
                'pct'   => count($catLessons) ? (int) round($catDone / count($catLessons) * 100) : 0,
            ];
        }

        return view('dashboard.index', compact(
            'risk', 'scoreDelta', 'breakdown',
            'quizCount', 'quizAvg', 'lessonsDone', 'totalLessons',
            'scanCount', 'highRiskScans', 'recentScans', 'recentQuizzes',
            'nextLesson', 'continue', 'rank', 'totalRanked',
            'week', 'weekMax', 'weekTotal', 'streak', 'achievements'
        ));
    }

    public function generateRiskScore(Request $request, RiskScoreService $service)
    {
        $userId = $request->user()->id;
        $result = $service->calculate($userId);

        RiskScore::create([
            'user_id'         => $userId,
            'score'           => $result['score'],
            'level'           => $result['level'],
            'quiz_weight'     => $result['quiz_weight'],
            'behavior_weight' => $result['behavior_weight'],
        ]);

        return back()->with('status', 'Your risk score has been recalculated.');
    }
}
