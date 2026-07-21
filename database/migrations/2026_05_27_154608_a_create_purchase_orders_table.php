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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained('purchase_quotes')->nullOnDelete();
            $table->foreignId('supplier_id')->constrained('third_parties')->cascadeOnDelete();
            $table->string('order_number');
            $table->date('date');
            $table->string('status')->default('DRAFT');
            $table->unsignedBigInteger('source_sale_id')->nullable();
            $table->boolean('is_master_data')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
