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
        Schema::create('pos_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('pos_register_id')->constrained('pos_registers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Operador
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0); // Fundo maneio
            $table->decimal('closing_balance', 15, 2)->nullable(); // Contagem física
            $table->decimal('calculated_balance', 15, 2)->nullable(); // Calculado pelo sistema
            $table->decimal('difference', 15, 2)->nullable();
            $table->string('status')->default('OPEN'); // OPEN, CLOSED
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_sessions');
    }
};
