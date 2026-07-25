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
        Schema::table('inventory_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_sessions', 'date')) {
                $table->date('date')->nullable()->after('scheduled_date');
            }
            if (!Schema::hasColumn('inventory_sessions', 'responsible_name')) {
                $table->string('responsible_name')->nullable()->after('created_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_sessions', 'date')) {
                $table->dropColumn('date');
            }
            if (Schema::hasColumn('inventory_sessions', 'responsible_name')) {
                $table->dropColumn('responsible_name');
            }
        });
    }
};
