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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->string('parent_type'); // Request, Quote, Order, Delivery, Invoice
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('received_qty', 15, 2)->default(0);
            $table->boolean('is_master_data')->default(false);
            $table->timestamps();
            
            $table->index(['parent_id', 'parent_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
