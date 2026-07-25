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
        Schema::table('inventory_session_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_session_lines', 'system_qty')) {
                $table->decimal('system_qty', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('inventory_session_lines', 'counted_qty')) {
                $table->decimal('counted_qty', 15, 2)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_session_lines', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_session_lines', 'system_qty')) {
                $table->dropColumn('system_qty');
            }
            if (Schema::hasColumn('inventory_session_lines', 'counted_qty')) {
                $table->dropColumn('counted_qty');
            }
        });
    }
};
