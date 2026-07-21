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
        Schema::create('pos_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete(); // Armazém de desconto de stock
            $table->string('name'); // Ex: Caixa 1
            $table->string('terminal_id')->nullable(); // Para POS bancário
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('CLOSED'); // OPEN, CLOSED
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_registers');
    }
};
