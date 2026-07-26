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
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'unit_cost')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('unit_cost', 15, 2)->default(0)->after('unit_price');
            });
        }

        if (Schema::hasTable('sale_items') && !Schema::hasColumn('sale_items', 'unit_cost')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->decimal('unit_cost', 15, 2)->default(0)->after('unit_price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'unit_cost')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('unit_cost');
            });
        }

        if (Schema::hasTable('sale_items') && Schema::hasColumn('sale_items', 'unit_cost')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->dropColumn('unit_cost');
            });
        }
    }
};
