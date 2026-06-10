<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\QuizResult;
use App\Models\RiskScore;
use App\Models\User;
use App\Models\UserLessonProgress;
use Illuminate\Http\Request;

class WebLeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $rows = User::where('role', 'user')->get()->map(function (User $user) {
            $avgQuiz      = QuizResult::where('user_id', $user->id)->avg('percentage');
            $quizzesTaken = QuizResult::where('user_id', $user->id)->count();
            $lessonsDone  = UserLessonProgress::where('user_id', $user->id)
                                ->where('is_completed', true)->count();
            $risk = RiskScore::where('user_id', $user->id)->latest()->first();

            return [
                'user_id'        => $user->id,
                'name'           => $user->name,
                'avg_quiz_score' => round($avgQuiz ?? 0, 1),
                'quizzes_taken'  => $quizzesTaken,
                'lessons_done'   => $lessonsDone,
                'risk_level'     => $risk?->level ?? 'unrated',
                'points'         => round(($avgQuiz ?? 0) + ($lessonsDone * 2), 1),
            ];
        })
        ->filter(fn ($r) => $r['quizzes_taken'] > 0)
        ->sortByDesc('points')
        ->values();

        $ranked = $rows->map(function ($r, $i) {
            $r['rank'] = $i + 1;
            return $r;
        });

        return view('leaderboard.index', [
            'rows' => $ranked,
            'myId' => $request->user()->id,
        ]);
    }
}
