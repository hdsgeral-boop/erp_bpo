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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('third_parties')->cascadeOnDelete();
            $table->string('receipt_number');
            $table->date('date');
            $table->decimal('total_amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->boolean('is_posted')->default(false);
            $table->string('payment_reference')->nullable();
            $table->boolean('is_master_data')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
