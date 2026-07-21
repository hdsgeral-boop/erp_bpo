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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->string('account_code')->nullable();
            $table->string('account_iva')->nullable();
            $table->string('account_iva_liquidado')->nullable();
            $table->string('account_iva_dedutivel')->nullable();
            $table->decimal('stock_qty', 15, 2)->default(0);
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->boolean('is_inventory')->default(true);
            $table->boolean('is_master_data')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
