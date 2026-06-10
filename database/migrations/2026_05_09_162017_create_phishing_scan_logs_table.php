<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('phishing_scan_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
        $table->enum('input_type', ['email', 'url']);
        $table->text('input_data');
        $table->enum('risk_level', ['low', 'medium', 'high']);
        $table->integer('risk_score');
        $table->json('flags')->nullable();
        $table->timestamp('scanned_at')->useCurrent();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phishing_scan_logs');
    }
};
