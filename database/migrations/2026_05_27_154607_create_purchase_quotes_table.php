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
        Schema::create('purchase_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('request_id')->nullable()->constrained('purchase_requests')->nullOnDelete();
            $table->foreignId('supplier_id')->constrained('third_parties')->cascadeOnDelete();
            $table->string('reference')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('DRAFT');
            $table->boolean('is_master_data')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_quotes');
    }
};
