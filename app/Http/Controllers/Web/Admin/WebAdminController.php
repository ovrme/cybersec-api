<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttackSimulation;
use App\Models\Lesson;
use App\Models\LessonSection;
use App\Models\PhishingScanLog;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Report;
use App\Models\RiskScore;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Tip;

class WebAdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalUsers'   => User::count(),
            'totalLessons' => Lesson::count(),
            'totalQuizzes' => Quiz::count(),
            'totalScans'   => PhishingScanLog::count(),
            'totalReports' => Report::count(),
            'risk' => [
                'high'   => RiskScore::where('level', 'high')->count(),
                'medium' => RiskScore::where('level', 'medium')->count(),
                'low'    => RiskScore::where('level', 'low')->count(),
            ],
            'recentUsers' => User::latest()->take(6)->get(),
        ]);
    }

    /* ---------------- Users ---------------- */

    public function users()
    {
        $users = User::with('profile')->latest()->paginate(15);

        return view('admin.users', compact('users'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'role'  => 'sometimes|in:user,admin',
        ]);

        $user->update($request->only('name', 'email', 'role'));

        return back()->with('status', 'User updated.');
    }

    public function deleteUser($id)
    {
        if ((int) $id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        User::findOrFail($id)->delete();

        return back()->with('status', 'User deleted.');
    }

    /* ---------------- Lessons ---------------- */

    public function lessons()
    {
        $lessons = Lesson::withCount('sections')->latest()->paginate(15);

        return view('admin.lessons', compact('lessons'));
    }

    public function storeLesson(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'summary'          => 'nullable|string|max:255',
            'content'          => 'required|string',
            'category'         => 'nullable|string|max:100',
            'difficulty'       => 'required|in:beginner,intermediate,advanced',
            'duration_minutes' => 'nullable|integer|min:1|max:240',
            'points'           => 'nullable|integer|min:0|max:1000',
        ]);

        Lesson::create($request->only(
            'title', 'summary', 'content', 'category', 'difficulty',
            'duration_minutes', 'points',
        ) + [
            'is_active'   => true,
            'order_index' => (int) (Lesson::max('order_index') + 1),
        ]);

        return back()->with('status', 'Lesson created.');
    }

    public function updateLesson(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $request->validate([
            'title'            => 'sometimes|string|max:255',
            'summary'          => 'nullable|string|max:255',
            'content'          => 'sometimes|string',
            'category'         => 'nullable|string|max:100',
            'difficulty'       => 'sometimes|in:beginner,intermediate,advanced',
            'duration_minutes' => 'nullable|integer|min:1|max:240',
            'points'           => 'nullable|integer|min:0|max:1000',
            'order_index'      => 'sometimes|integer|min:0',
            'is_active'        => 'sometimes|boolean',
        ]);

        $lesson->update($request->only(
            'title', 'summary', 'content', 'category', 'difficulty',
            'duration_minutes', 'points', 'order_index', 'is_active',
        ));

        return back()->with('status', 'Lesson updated.');
    }

    public function deleteLesson($id)
    {
        Lesson::findOrFail($id)->delete();

        return back()->with('status', 'Lesson deleted.');
    }

    /* ---------------- Lesson sections ---------------- */

    public function lessonSections($id)
    {
        $lesson = Lesson::with('sections')->findOrFail($id);

        return view('admin.lesson-sections', compact('lesson'));
    }

    public function storeSection(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $data = $this->validateSection($request);
        $data['lesson_id']  = $lesson->id;
        $data['sort_order'] = $request->integer('sort_order')
            ?: (int) (LessonSection::where('lesson_id', $lesson->id)->max('sort_order') + 1);

        LessonSection::create($data);

        return back()->with('status', 'Section added.');
    }

    public function updateSection(Request $request, $id)
    {
        $section = LessonSection::findOrFail($id);

        $data = $this->validateSection($request);
        if ($request->filled('sort_order')) {
            $data['sort_order'] = $request->integer('sort_order');
        }

        $section->update($data);

        return back()->with('status', 'Section updated.');
    }

    public function deleteSection($id)
    {
        LessonSection::findOrFail($id)->delete();

        return back()->with('status', 'Section deleted.');
    }

    /** Shared validation for create/update; decodes meta JSON for casting. */
    private function validateSection(Request $request): array
    {
        $request->validate([
            'type'  => 'required|in:read,cards,exercise,quiz',
            'label' => 'nullable|string|max:50',
            'title' => 'required|string|max:255',
            'body'  => 'nullable|string',
            'meta'  => 'nullable|json',
        ]);

        $meta = $request->filled('meta') ? json_decode($request->meta, true) : null;

        if ($request->type !== 'read' && empty($meta)) {
            abort(back()->withErrors([
                'meta' => 'Interactive sections (cards / exercise / quiz) need Meta JSON — see the examples below the form.',
            ]));
        }

        return [
            'type'  => $request->type,
            'label' => $request->label,
            'title' => $request->title,
            'body'  => (string) $request->body,
            'meta'  => $meta,
        ];
    }

    /* ---------------- Quizzes ---------------- */

    public function quizzes()
    {
        $quizzes = Quiz::withCount('questions')->latest()->paginate(15);
        $lessons = Lesson::orderBy('title')->get(['id', 'title']);

        return view('admin.quizzes', compact('quizzes', 'lessons'));
    }

    public function storeQuiz(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'lesson_id'   => 'nullable|exists:lessons,id',
        ]);

        Quiz::create($request->only('title', 'description', 'lesson_id') + ['is_active' => true]);

        return back()->with('status', 'Quiz created — now add its questions.');
    }

    public function updateQuiz(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);

        $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:500',
            'lesson_id'   => 'nullable|exists:lessons,id',
            'is_active'   => 'sometimes|boolean',
        ]);

        $quiz->update($request->only('title', 'description', 'lesson_id', 'is_active'));

        return back()->with('status', 'Quiz updated.');
    }

    public function deleteQuiz($id)
    {
        Quiz::findOrFail($id)->delete();

        return back()->with('status', 'Quiz deleted.');
    }

    /* ---------------- Quiz questions ---------------- */

    public function quizQuestions($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);

        return view('admin.quiz-questions', compact('quiz'));
    }

    public function storeQuestion(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);

        $data = $this->validateQuestion($request);
        $data['quiz_id'] = $quiz->id;

        QuizQuestion::create($data);

        return back()->with('status', 'Question added.');
    }

    public function updateQuestion(Request $request, $id)
    {
        $question = QuizQuestion::findOrFail($id);

        $question->update($this->validateQuestion($request));

        return back()->with('status', 'Question updated.');
    }

    public function deleteQuestion($id)
    {
        QuizQuestion::findOrFail($id)->delete();

        return back()->with('status', 'Question deleted.');
    }

    private function validateQuestion(Request $request): array
    {
        $request->validate([
            'question'       => 'required|string',
            'option_a'       => 'required|string|max:255',
            'option_b'       => 'required|string|max:255',
            'option_c'       => 'nullable|string|max:255',
            'option_d'       => 'nullable|string|max:255',
            'correct_answer' => 'required|in:a,b,c,d',
            'explanation'    => 'nullable|string',
        ]);

        // Correct answer must point at a filled option.
        $picked = $request->input('option_' . $request->correct_answer);
        if (blank($picked)) {
            abort(back()->withErrors([
                'correct_answer' => 'The correct answer points at an empty option.',
            ]));
        }

        return $request->only(
            'question', 'option_a', 'option_b', 'option_c', 'option_d',
            'correct_answer', 'explanation',
        );
    }

    /* ---------------- Simulations ---------------- */

    public function simulations()
    {
        $simulations = AttackSimulation::withCount('interactions')->latest()->paginate(15);

        return view('admin.simulations', compact('simulations'));
    }

    public function storeSimulation(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:phishing_email,fake_url,social_engineering',
            'description' => 'nullable|string|max:255',
            'content'     => 'required|string',
        ]);

        AttackSimulation::create($request->only('title', 'type', 'description', 'content') + ['is_active' => true]);

        return back()->with('status', 'Simulation created.');
    }

    public function updateSimulation(Request $request, $id)
    {
        $simulation = AttackSimulation::findOrFail($id);

        $request->validate([
            'title'       => 'sometimes|string|max:255',
            'type'        => 'sometimes|in:phishing_email,fake_url,social_engineering',
            'description' => 'nullable|string|max:255',
            'content'     => 'sometimes|string',
            'is_active'   => 'sometimes|boolean',
        ]);

        $simulation->update($request->only('title', 'type', 'description', 'content', 'is_active'));

        return back()->with('status', 'Simulation updated.');
    }

    public function deleteSimulation($id)
    {
        AttackSimulation::findOrFail($id)->delete();

        return back()->with('status', 'Simulation deleted.');
    }

    /* ---------------- Logs & Reports ---------------- */

    public function scanLogs()
    {
        $logs = PhishingScanLog::with('user')->latest()->paginate(20);

        return view('admin.scan-logs', compact('logs'));
    }

    public function reports()
    {
        $reports = Report::with('user')->latest()->paginate(20);

        return view('admin.reports', compact('reports'));
    }

    /* ---------------- Tips ---------------- */

    public function tips()
    {
        $tips = Tip::orderByRaw("FIELD(level, 'high', 'medium', 'low')")
            ->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low')")
            ->paginate(15);

        return view('admin.tips', compact('tips'));
    }

    public function storeTip(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'level'       => 'required|in:high,medium,low',
            'priority'    => 'required|in:critical,high,medium,low',
        ]);

        Tip::create($request->only('title', 'description', 'level', 'priority') + ['is_active' => true]);

        return back()->with('status', 'Tip created.');
    }

    public function updateTip(Request $request, $id)
    {
        $tip = Tip::findOrFail($id);

        $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:500',
            'level'       => 'sometimes|in:high,medium,low',
            'priority'    => 'sometimes|in:critical,high,medium,low',
            'is_active'   => 'sometimes|boolean',
        ]);

        $tip->update($request->only('title', 'description', 'level', 'priority', 'is_active'));

        return back()->with('status', 'Tip updated.');
    }

    public function deleteTip($id)
    {
        Tip::findOrFail($id)->delete();

        return back()->with('status', 'Tip deleted.');
    }
}
