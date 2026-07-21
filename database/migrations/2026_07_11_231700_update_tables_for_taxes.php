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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('tax_id')->nullable()->after('tax_rate')->constrained('taxes')->nullOnDelete();
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->foreignId('tax_id')->nullable()->after('tax_rate')->constrained('taxes')->nullOnDelete();
            $table->string('exemption_reason')->nullable()->after('tax_id');
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->foreignId('tax_id')->nullable()->after('tax_rate')->constrained('taxes')->nullOnDelete();
            $table->string('exemption_reason')->nullable()->after('tax_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['tax_id']);
            $table->dropColumn('tax_id');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['tax_id']);
            $table->dropColumn(['tax_id', 'exemption_reason']);
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropForeign(['tax_id']);
            $table->dropColumn(['tax_id', 'exemption_reason']);
        });
    }
};
