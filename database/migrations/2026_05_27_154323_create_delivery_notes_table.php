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
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('doc_number');
            $table->date('date');
            $table->string('type'); // Entrada, Saída
            $table->integer('entity_id')->nullable();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('receiving_area')->nullable();
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
        Schema::dropIfExists('delivery_notes');
    }
};
