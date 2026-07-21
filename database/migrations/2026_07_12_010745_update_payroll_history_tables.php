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
        Schema::table('payroll_runs', function (Blueprint $table) {
            $table->integer('version')->default(1)->after('year');
            $table->boolean('is_reversed')->default(false)->after('status');
            $table->timestamp('reversed_at')->nullable()->after('is_reversed');
            $table->foreignId('reversed_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_runs', function (Blueprint $table) {
            $table->dropForeign(['reversed_by']);
            $table->dropColumn(['version', 'is_reversed', 'reversed_at', 'reversed_by']);
        });
    }
};
