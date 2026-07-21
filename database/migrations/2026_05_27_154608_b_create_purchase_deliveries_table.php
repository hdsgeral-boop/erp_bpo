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
        Schema::create('purchase_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->string('delivery_number');
            $table->date('date');
            $table->string('status')->default('DRAFT');
            $table->boolean('is_posted')->default(false);
            $table->boolean('is_validated')->default(false);
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->boolean('is_master_data')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_deliveries');
    }
};
