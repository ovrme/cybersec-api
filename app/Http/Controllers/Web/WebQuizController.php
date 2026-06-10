<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizResult;
use Illuminate\Http\Request;

class WebQuizController extends Controller
{
    public function index(Request $request)
    {
        $quizzes = Quiz::where('is_active', true)->withCount('questions')->get();

        // quiz_id => { best, attempts, passed } for the current user
        $stats = QuizResult::where('user_id', $request->user()->id)
            ->selectRaw('quiz_id, MAX(percentage) as best, COUNT(*) as attempts')
            ->groupBy('quiz_id')
            ->get()
            ->keyBy('quiz_id');

        return view('quizzes.index', compact('quizzes', 'stats'));
    }

    public function show($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);

        return view('quizzes.show', compact('quiz'));
    }

    public function submit(Request $request, $id)
    {
        $request->validate(['answers' => 'required|array']);

        $quiz    = Quiz::with('questions')->findOrFail($id);
        $answers = $request->answers;
        $score   = 0;
        $results = [];

        foreach ($quiz->questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $isCorrect  = $userAnswer === $question->correct_answer;
            if ($isCorrect) {
                $score++;
            }

            $results[] = [
                'id'             => $question->id,   // lets the result page show option text
                'question'       => $question->question,
                'your_answer'    => $userAnswer,
                'correct_answer' => $question->correct_answer,
                'is_correct'     => $isCorrect,
                'explanation'    => $question->explanation,
            ];
        }

        $total      = $quiz->questions->count();
        $percentage = $total > 0 ? round(($score / $total) * 100, 2) : 0;

        QuizResult::create([
            'user_id'    => $request->user()->id,
            'quiz_id'    => $id,
            'score'      => $score,
            'total'      => $total,
            'percentage' => $percentage,
        ]);

        return view('quizzes.result', [
            'quiz'       => $quiz,
            'score'      => $score,
            'total'      => $total,
            'percentage' => $percentage,
            'results'    => $results,
            'passed'     => $percentage >= 70,
        ]);
    }
}
