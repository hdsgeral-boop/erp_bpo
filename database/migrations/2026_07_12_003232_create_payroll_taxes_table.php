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
        Schema::create('payroll_taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // ex: 'INSS'
            $table->decimal('employee_rate', 5, 2)->default(0);
            $table->decimal('employer_rate', 5, 2)->default(0);
            $table->date('valid_from');
            $table->date('valid_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_taxes');
    }
};
