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
        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->default(0)->after('quantity');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('unit_price');
            $table->decimal('subtotal', 15, 2)->default(0)->after('tax_amount');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->default(0)->after('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'tax_amount', 'subtotal']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('total_amount');
        });
    }
};
