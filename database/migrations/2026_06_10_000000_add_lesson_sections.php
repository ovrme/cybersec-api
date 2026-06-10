<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('summary')->nullable()->after('title');
            $table->unsignedSmallInteger('duration_minutes')->nullable()->after('summary');
            $table->unsignedSmallInteger('points')->nullable()->after('duration_minutes');
        });

        Schema::create('lesson_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('label')->nullable();
            $table->string('title');
            $table->longText('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_sections');
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['summary', 'duration_minutes', 'points']);
        });
    }
};
