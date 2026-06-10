<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\PhishingScanLog;
use App\Models\Quiz;
use App\Models\QuizResult;
use App\Models\Report;
use App\Models\RiskScore;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // --- Dashboard Overview ---
    public function dashboard()
    {
        return response()->json([
            'total_users'   => User::count(),
            'total_lessons' => Lesson::count(),
            'total_quizzes' => Quiz::count(),
            'total_scans'   => PhishingScanLog::count(),
            'total_reports' => Report::count(),
            'risk_overview' => [
                'high'   => RiskScore::where('level', 'high')->count(),
                'medium' => RiskScore::where('level', 'medium')->count(),
                'low'    => RiskScore::where('level', 'low')->count(),
            ],
            'recent_users'  => User::latest()->take(5)->get(),
        ]);
    }

    // --- User Management ---
    public function users()
    {
        $users = User::with('profile')
                        ->withCount(['profile'])
                        ->latest()
                        ->get();
        return response()->json($users);
    }

    public function showUser($id)
    {
        $user = User::with('profile')->findOrFail($id);
        return response()->json($user);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'role'  => 'sometimes|in:user,admin',
        ]);

        $user->update($request->only('name', 'email', 'role'));

        return response()->json([
            'message' => 'User updated successfully',
            'user'    => $user,
        ]);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    // --- Lesson Management ---
    public function lessons()
    {
        return response()->json(Lesson::latest()->get());
    }

    public function updateLesson(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->update($request->all());

        return response()->json([
            'message' => 'Lesson updated successfully',
            'lesson'  => $lesson,
        ]);
    }

    public function deleteLesson($id)
    {
        Lesson::findOrFail($id)->delete();
        return response()->json(['message' => 'Lesson deleted successfully']);
    }

    // --- Quiz Management ---
    public function quizzes()
    {
        return response()->json(
            Quiz::withCount('questions')->latest()->get()
        );
    }

    public function deleteQuiz($id)
    {
        Quiz::findOrFail($id)->delete();
        return response()->json(['message' => 'Quiz deleted successfully']);
    }

    // --- Scan Logs ---
    public function scanLogs()
    {
        $logs = PhishingScanLog::with('user')
                                ->latest()
                                ->get();
        return response()->json($logs);
    }

    // --- Reports ---
    public function reports()
    {
        $reports = Report::with('user')
                            ->latest()
                            ->get();
        return response()->json($reports);
    }

    // --- Make user admin ---
    public function makeAdmin($id)
    {
        $user = User::findOrFail($id);
        $user->update(['role' => 'admin']);

        return response()->json([
            'message' => $user->name . ' is now an admin',
            'user'    => $user,
        ]);
    }
}
