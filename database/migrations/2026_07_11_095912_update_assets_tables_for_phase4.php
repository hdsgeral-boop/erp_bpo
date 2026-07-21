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
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->after('vendor_id')->constrained()->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->after('department_id')->constrained()->nullOnDelete();
        });
        
        // Atribuir company_id default se houver dados
        \Illuminate\Support\Facades\DB::table('asset_categories')->update(['company_id' => 1]);
        \Illuminate\Support\Facades\DB::table('fixed_assets')->update(['company_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['employee_id']);
            $table->dropColumn(['company_id', 'department_id', 'employee_id']);
        });

        Schema::table('asset_categories', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
