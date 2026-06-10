<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tips', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description', 500);
            // Which risk level sees this tip (high-risk users also see medium tips)
            $table->enum('level', ['high', 'medium', 'low'])->default('medium');
            // Urgency badge shown on the card
            $table->enum('priority', ['critical', 'high', 'medium', 'low'])->default('medium');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tips');
    }
};
