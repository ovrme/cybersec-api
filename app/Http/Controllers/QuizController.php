<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizResult;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    // Get all quizzes
    public function index()
    {
        $quizzes = Quiz::where('is_active', true)
                        ->withCount('questions')
                        ->get();
        return response()->json($quizzes);
    }

    // Get single quiz with questions
    public function show($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);
        return response()->json($quiz);
    }

    // Submit quiz answers
    public function submit(Request $request, $id)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        $quiz      = Quiz::with('questions')->findOrFail($id);
        $answers   = $request->answers;
        $score     = 0;
        $results   = [];

        foreach ($quiz->questions as $question) {
            $userAnswer    = $answers[$question->id] ?? null;
            $isCorrect     = $userAnswer === $question->correct_answer;

            if ($isCorrect) $score++;

            $results[] = [
                'question_id'    => $question->id,
                'question'       => $question->question,
                'your_answer'    => $userAnswer,
                'correct_answer' => $question->correct_answer,
                'is_correct'     => $isCorrect,
                'explanation'    => $question->explanation,
            ];
        }

        $total      = $quiz->questions->count();
        $percentage = $total > 0 ? round(($score / $total) * 100, 2) : 0;

        // Save result
        QuizResult::create([
            'user_id'    => $request->user()->id,
            'quiz_id'    => $id,
            'score'      => $score,
            'total'      => $total,
            'percentage' => $percentage,
        ]);

        return response()->json([
            'score'      => $score,
            'total'      => $total,
            'percentage' => $percentage,
            'results'    => $results,
            'message'    => $percentage >= 70
                            ? '🎉 Great job! You passed!'
                            : '📚 Keep studying and try again!',
        ]);
    }

    // Admin: create quiz
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string',
            'description' => 'nullable|string',
            'questions'   => 'required|array|min:1',
        ]);

        $quiz = Quiz::create($request->only('title', 'description', 'lesson_id'));

        foreach ($request->questions as $q) {
            $quiz->questions()->create($q);
        }

        return response()->json([
            'message' => 'Quiz created successfully',
            'quiz'    => $quiz->load('questions'),
        ], 201);
    }

    // Get user quiz history
    public function history(Request $request)
    {
        $results = QuizResult::where('user_id', $request->user()->id)
                                ->with('quiz')
                                ->latest()
                                ->get();
        return response()->json($results);
    }
}
