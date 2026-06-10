<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\UserLessonProgress;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    // Get all lessons
    public function index()
    {
        $lessons = Lesson::where('is_active', true)
                            ->orderBy('order_index')
                            ->get();

        return response()->json($lessons);
    }

    // Get single lesson
    public function show($id)
    {
        $lesson = Lesson::findOrFail($id);
        return response()->json($lesson);
    }

    // Mark lesson as complete
    public function complete(Request $request, $id)
    {
        $progress = UserLessonProgress::updateOrCreate(
            [
                'user_id'   => $request->user()->id,
                'lesson_id' => $id,
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        return response()->json([
            'message'  => 'Lesson marked as completed',
            'progress' => $progress,
        ]);
    }

    // Get user progress
    public function progress(Request $request)
    {
        $progress = UserLessonProgress::where('user_id', $request->user()->id)
                                        ->with('lesson')
                                        ->get();

        return response()->json($progress);
    }

    // Admin: create lesson
    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string',
            'content'    => 'required|string',
            'category'   => 'nullable|string',
            'difficulty' => 'in:beginner,intermediate,advanced',
        ]);

        $lesson = Lesson::create($request->all());

        return response()->json([
            'message' => 'Lesson created successfully',
            'lesson'  => $lesson,
        ], 201);
    }
}
