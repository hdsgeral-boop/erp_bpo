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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('contract_type')->nullable()->after('inss');
            $table->string('fiscal_regime')->nullable()->after('contract_type');
            $table->string('civil_status')->nullable()->after('fiscal_regime');
            $table->integer('dependents')->default(0)->after('civil_status');
            $table->string('municipality')->nullable()->after('dependents');
            $table->string('province')->nullable()->after('municipality');
            $table->foreignId('cost_center_id')->nullable()->after('position_id')->constrained('cost_centers')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->after('cost_center_id')->constrained('categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['cost_center_id']);
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'first_name', 'last_name', 'contract_type', 'fiscal_regime', 
                'civil_status', 'dependents', 'municipality', 'province', 
                'cost_center_id', 'category_id'
            ]);
        });
    }
};
