<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a one-line description to attack simulations.
     * Shown on the user-facing simulation cards and the
     * simulation hero; editable from the admin panel.
     */
    public function up(): void
    {
        Schema::table('attack_simulations', function (Blueprint $table) {
            $table->string('description')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('attack_simulations', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
