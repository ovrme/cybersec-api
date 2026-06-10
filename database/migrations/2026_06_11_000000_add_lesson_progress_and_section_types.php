<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_lesson_progress', function (Blueprint $table) {
            if (! Schema::hasColumn('user_lesson_progress', 'progress_percent')) {
                $table->unsignedTinyInteger('progress_percent')->default(0)->after('is_completed');
            }
            if (! Schema::hasColumn('user_lesson_progress', 'current_section')) {
                $table->unsignedTinyInteger('current_section')->default(0)->after('progress_percent');
            }
        });

        Schema::table('lesson_sections', function (Blueprint $table) {
            if (! Schema::hasColumn('lesson_sections', 'type')) {
                $table->string('type', 20)->default('read')->after('sort_order');
            }
            if (! Schema::hasColumn('lesson_sections', 'meta')) {
                $table->json('meta')->nullable()->after('body');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_lesson_progress', fn (Blueprint $t) => $t->dropColumn(['progress_percent', 'current_section']));
        Schema::table('lesson_sections', fn (Blueprint $t) => $t->dropColumn(['type', 'meta']));
    }
};
