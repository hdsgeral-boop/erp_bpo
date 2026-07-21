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
        Schema::create('waybills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('third_parties');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->string('document_number')->unique();
            $table->date('date');
            $table->string('vehicle_plate')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('status')->default('DRAFT'); // DRAFT, FINAL, CANCELED
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waybills');
    }
};
