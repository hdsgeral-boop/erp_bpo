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
        Schema::create('payroll_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->decimal('other_additions', 15, 2)->default(0);
            $table->decimal('inss_base', 15, 2)->default(0);
            $table->decimal('inss_employee', 15, 2)->default(0);
            $table->decimal('inss_company', 15, 2)->default(0);
            $table->decimal('irt', 15, 2)->default(0);
            $table->decimal('other_deductions', 15, 2)->default(0);
            $table->decimal('net_total', 15, 2)->default(0);
            $table->json('details')->nullable(); // Guardar detalhes dos infotipos aplicados
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_receipts');
    }
};
