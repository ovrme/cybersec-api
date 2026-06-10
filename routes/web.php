<?php

use App\Http\Controllers\Web\Admin\WebAdminController;
use App\Http\Controllers\Web\WebAuthController;
use App\Http\Controllers\Web\WebDashboardController;
use App\Http\Controllers\Web\WebLeaderboardController;
use App\Http\Controllers\Web\WebLessonController;
use App\Http\Controllers\Web\WebProfileController;
use App\Http\Controllers\Web\WebQuizController;
use App\Http\Controllers\Web\WebRecommendationController;
use App\Http\Controllers\Web\WebReportController;
use App\Http\Controllers\Web\WebScannerController;
use App\Http\Controllers\Web\WebSimulationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => Auth::check()
    ? redirect()->route('dashboard')
    : view('welcome'))->name('home');

/* ---------------- Guest ---------------- */
Route::middleware('guest')->group(function () {
    Route::get('/login',     [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [WebAuthController::class, 'login']);
    Route::get('/register',  [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register']);
});

Route::post('/logout', [WebAuthController::class, 'logout'])->middleware('auth')->name('logout');

/* ---------------- Email verification ---------------- */
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('status', 'Email verified — welcome aboard.');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent.');
    })->middleware('throttle:6,1')->name('verification.send');
});

/* ---------------- Authenticated + verified ---------------- */
Route::middleware(['auth'])->group(function () {

    // 10. Dashboard + 7. Risk score
    Route::get('/dashboard', [WebDashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/risk-score', [WebDashboardController::class, 'generateRiskScore'])->name('risk-score.generate');

    // 2. Profile
    Route::get('/profile', [WebProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [WebProfileController::class, 'update'])->name('profile.update');

    // 3. Lessons
    Route::get('/lessons', [WebLessonController::class, 'index'])->name('lessons.index');
    Route::get('/lessons/{id}', [WebLessonController::class, 'show'])->whereNumber('id')->name('lessons.show');
    Route::post('/lessons/{id}/progress', [WebLessonController::class, 'progress'])->whereNumber('id')->name('lessons.progress');
    Route::post('/lessons/{id}/complete', [WebLessonController::class, 'complete'])->whereNumber('id')->name('lessons.complete');

    // 4 + 5. Scanner
    Route::get('/scanner', [WebScannerController::class, 'index'])->name('scanner.index');
    Route::post('/scanner/email', [WebScannerController::class, 'scanEmail'])->name('scanner.email');
    Route::post('/scanner/url', [WebScannerController::class, 'scanUrl'])->name('scanner.url');

    // 6. Quizzes
    Route::get('/quizzes', [WebQuizController::class, 'index'])->name('quizzes.index');
    Route::get('/quizzes/{id}', [WebQuizController::class, 'show'])->whereNumber('id')->name('quizzes.show');
    Route::post('/quizzes/{id}/submit', [WebQuizController::class, 'submit'])->whereNumber('id')->name('quizzes.submit');

    // 9. Simulations
    Route::get('/simulations', [WebSimulationController::class, 'index'])->name('simulations.index');
    Route::get('/simulations/{id}', [WebSimulationController::class, 'show'])->whereNumber('id')->name('simulations.show');
    Route::post('/simulations/{id}/interact', [WebSimulationController::class, 'interact'])->whereNumber('id')->name('simulations.interact');

    // 8. Recommendations
    Route::get('/tips', [WebRecommendationController::class, 'index'])->name('recommendations.index');

    // 11. Reports
    Route::get('/reports', [WebReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [WebReportController::class, 'generate'])->name('reports.generate');

    // Leaderboard
    Route::get('/leaderboard', [WebLeaderboardController::class, 'index'])->name('leaderboard.index');
});

/* ---------------- 12. Admin Panel ---------------- */
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [WebAdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/users', [WebAdminController::class, 'users'])->name('users');
    Route::put('/users/{id}', [WebAdminController::class, 'updateUser'])->whereNumber('id')->name('users.update');
    Route::delete('/users/{id}', [WebAdminController::class, 'deleteUser'])->whereNumber('id')->name('users.delete');

    // Tips
    Route::get('/tips', [WebAdminController::class, 'tips'])->name('tips');
    Route::post('/tips', [WebAdminController::class, 'storeTip'])->name('tips.store');
    Route::put('/tips/{id}', [WebAdminController::class, 'updateTip'])->whereNumber('id')->name('tips.update');
    Route::delete('/tips/{id}', [WebAdminController::class, 'deleteTip'])->whereNumber('id')->name('tips.delete');

    // Lessons
    Route::get('/lessons', [WebAdminController::class, 'lessons'])->name('lessons');
    Route::post('/lessons', [WebAdminController::class, 'storeLesson'])->name('lessons.store');
    Route::put('/lessons/{id}', [WebAdminController::class, 'updateLesson'])->whereNumber('id')->name('lessons.update');
    Route::delete('/lessons/{id}', [WebAdminController::class, 'deleteLesson'])->whereNumber('id')->name('lessons.delete');

    // Lesson sections
    Route::get('/lessons/{id}/sections', [WebAdminController::class, 'lessonSections'])->whereNumber('id')->name('lessons.sections');
    Route::post('/lessons/{id}/sections', [WebAdminController::class, 'storeSection'])->whereNumber('id')->name('lessons.sections.store');
    Route::put('/sections/{id}', [WebAdminController::class, 'updateSection'])->whereNumber('id')->name('lessons.sections.update');
    Route::delete('/sections/{id}', [WebAdminController::class, 'deleteSection'])->whereNumber('id')->name('lessons.sections.delete');

    // Quizzes + questions
    Route::get('/quizzes', [WebAdminController::class, 'quizzes'])->name('quizzes');
    Route::post('/quizzes', [WebAdminController::class, 'storeQuiz'])->name('quizzes.store');
    Route::put('/quizzes/{id}', [WebAdminController::class, 'updateQuiz'])->whereNumber('id')->name('quizzes.update');
    Route::delete('/quizzes/{id}', [WebAdminController::class, 'deleteQuiz'])->whereNumber('id')->name('quizzes.delete');
    Route::get('/quizzes/{id}/questions', [WebAdminController::class, 'quizQuestions'])->whereNumber('id')->name('quizzes.questions');
    Route::post('/quizzes/{id}/questions', [WebAdminController::class, 'storeQuestion'])->whereNumber('id')->name('quizzes.questions.store');
    Route::put('/questions/{id}', [WebAdminController::class, 'updateQuestion'])->whereNumber('id')->name('quizzes.questions.update');
    Route::delete('/questions/{id}', [WebAdminController::class, 'deleteQuestion'])->whereNumber('id')->name('quizzes.questions.delete');

    // Simulations
    Route::get('/simulations', [WebAdminController::class, 'simulations'])->name('simulations');
    Route::post('/simulations', [WebAdminController::class, 'storeSimulation'])->name('simulations.store');
    Route::put('/simulations/{id}', [WebAdminController::class, 'updateSimulation'])->whereNumber('id')->name('simulations.update');
    Route::delete('/simulations/{id}', [WebAdminController::class, 'deleteSimulation'])->whereNumber('id')->name('simulations.delete');

    Route::get('/scan-logs', [WebAdminController::class, 'scanLogs'])->name('scan-logs');
    Route::get('/reports', [WebAdminController::class, 'reports'])->name('reports');
});
