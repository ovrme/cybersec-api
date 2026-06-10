<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Per-lesson resume state: how far into the lesson the user is.
        Schema::table('user_lesson_progress', function (Blueprint $table) {
            $table->unsignedTinyInteger('progress_percent')->default(0)->after('is_completed');
            $table->unsignedTinyInteger('current_section')->default(0)->after('progress_percent');
        });

        // Typed sections: 'read' (default, current behaviour), 'cards',
        // 'exercise', 'quiz'. Interactive types read their data from `meta`.
        Schema::table('lesson_sections', function (Blueprint $table) {
            $table->string('type', 20)->default('read')->after('sort_order');
            $table->json('meta')->nullable()->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('user_lesson_progress', function (Blueprint $table) {
            $table->dropColumn(['progress_percent', 'current_section']);
        });
        Schema::table('lesson_sections', function (Blueprint $table) {
            $table->dropColumn(['type', 'meta']);
        });
    }
};
