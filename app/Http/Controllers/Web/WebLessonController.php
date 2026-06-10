<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\RiskScore;
use App\Models\UserLessonProgress;
use App\Services\RiskScoreService;
use Illuminate\Http\Request;

class WebLessonController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = Lesson::where('is_active', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(fn ($q) => $q
                ->where('title', 'like', "%{$term}%")
                ->orWhere('content', 'like', "%{$term}%"));
        }

        $lessons = $query->orderBy('order_index')->get();

        $completedIds = UserLessonProgress::where('user_id', $userId)
            ->where('is_completed', true)->pluck('lesson_id')->all();

        // lesson_id => percent, for in-progress lessons only
        $progressMap = UserLessonProgress::where('user_id', $userId)
            ->where('is_completed', false)
            ->where('progress_percent', '>', 0)
            ->pluck('progress_percent', 'lesson_id')->all();

        // ---- Sorting ----
        $sort = $request->input('sort', 'rec');
        if ($sort === 'az') {
            $lessons = $lessons->sortBy('title')->values();
        } elseif ($sort === 'short') {
            $lessons = $lessons->sortBy(fn ($l) => $l->duration_minutes ?? 999)->values();
        } else {
            // Recommended: in-progress first, then new, completed last;
            // order_index inside each group.
            $lessons = $lessons->sortBy(function ($l) use ($completedIds, $progressMap) {
                $group = isset($progressMap[$l->id]) ? 0
                       : (in_array($l->id, $completedIds) ? 2 : 1);

                return sprintf('%d-%05d', $group, $l->order_index);
            })->values();
        }

        $categories = Lesson::where('is_active', true)->whereNotNull('category')
            ->distinct()->pluck('category');

        $catCounts = Lesson::where('is_active', true)->whereNotNull('category')
            ->selectRaw('category, count(*) as c')->groupBy('category')
            ->pluck('c', 'category')->all();
        $totalAll     = Lesson::where('is_active', true)->count();
        $totalLessons = $totalAll;
        $doneCount    = count($completedIds);

        // ---- Resume strip: real in-lesson progress ----
        $resume = null;
        if (! empty($progressMap)) {
            // Lesson with the most progress wins.
            arsort($progressMap);
            $resumeId     = array_key_first($progressMap);
            $resumeLesson = Lesson::find($resumeId);
            if ($resumeLesson) {
                $resume = [
                    'id'    => $resumeLesson->id,
                    'title' => $resumeLesson->title,
                    'pct'   => (int) $progressMap[$resumeId],
                ];
            }
            arsort($progressMap); // keep map intact for the cards
        } elseif ($doneCount > 0 && $doneCount < $totalLessons) {
            // Nothing mid-lesson, but there's a next lesson to start.
            $next = Lesson::where('is_active', true)
                ->whereNotIn('id', $completedIds)
                ->orderBy('order_index')->first();
            if ($next) {
                $resume = ['id' => $next->id, 'title' => $next->title, 'pct' => 0];
            }
        }

        return view('lessons.index', [
            'lessons'          => $lessons,
            'categories'       => $categories,
            'completedIds'     => $completedIds,
            'progressMap'      => $progressMap,
            'catCounts'        => $catCounts,
            'totalAll'         => $totalAll,
            'totalLessons'     => $totalLessons,
            'doneCount'        => $doneCount,
            'resume'           => $resume,
            'activeCategory'   => $request->category,
            'activeDifficulty' => $request->difficulty,
            'activeSort'       => $sort,
            'search'           => $request->search,
        ]);
    }

    public function show(Request $request, $id)
    {
        $lesson = Lesson::where('is_active', true)
            ->with('sections')
            ->findOrFail($id);

        $progress = UserLessonProgress::where('user_id', $request->user()->id)
            ->where('lesson_id', $id)->first();

        $completed = (bool) ($progress?->is_completed);

        // Where to resume (clamped so a deleted section can't break it).
        $sectionCount = max($lesson->sections->count(), 1);
        $startSection = $completed ? 0 : min((int) ($progress->current_section ?? 0), $sectionCount - 1);

        return view('lessons.show', compact('lesson', 'completed', 'startSection'));
    }

    /**
     * Called by the lesson page (fetch) each time a part is finished,
     * so progress survives refreshes and powers the resume strip.
     */
    public function progress(Request $request, $id)
    {
        $data = $request->validate([
            'current_section'  => 'required|integer|min:0|max:255',
            'progress_percent' => 'required|integer|min:0|max:100',
        ]);

        Lesson::where('is_active', true)->findOrFail($id);

        $row = UserLessonProgress::firstOrNew([
            'user_id'   => $request->user()->id,
            'lesson_id' => $id,
        ]);

        // Only ever move forward; never let a stale tab roll progress back.
        if (! $row->is_completed) {
            $row->current_section  = max((int) $row->current_section, $data['current_section']);
            $row->progress_percent = max((int) $row->progress_percent, $data['progress_percent']);
            $row->save();
        }

        return response()->json(['ok' => true]);
    }

    public function complete(Request $request, $id, RiskScoreService $service)
    {
        $lesson = Lesson::findOrFail($id);
        $userId = $request->user()->id;

        UserLessonProgress::updateOrCreate(
            ['user_id' => $userId, 'lesson_id' => $id],
            [
                'is_completed'     => true,
                'completed_at'     => now(),
                'progress_percent' => 100,
            ],
        );

        // Lesson completion changes behaviour, so refresh the risk score.
        $result = $service->calculate($userId);
        RiskScore::create([
            'user_id'         => $userId,
            'score'           => $result['score'],
            'level'           => $result['level'],
            'quiz_weight'     => $result['quiz_weight'],
            'behavior_weight' => $result['behavior_weight'],
        ]);

        $pts = $lesson->points ? " +{$lesson->points} pts earned." : '';

        return redirect()->route('lessons.index')
            ->with('status', "Lesson complete — \"{$lesson->title}\".{$pts}");
    }
}
