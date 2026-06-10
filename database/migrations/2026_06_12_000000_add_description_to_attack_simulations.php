<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('attack_simulations', 'description')) {
            Schema::table('attack_simulations', function (Blueprint $table) {
                $table->string('description')->nullable()->after('type');
            });
        }
    }

    public function down(): void
    {
        Schema::table('attack_simulations', fn (Blueprint $t) => $t->dropColumn('description'));
    }
};
