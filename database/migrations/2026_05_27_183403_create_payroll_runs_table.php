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
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('reference'); // e.g. "05-2026"
            $table->integer('month');
            $table->integer('year');
            $table->string('status')->default('DRAFT'); // DRAFT, PROCESSED, PAID
            $table->decimal('total_base', 15, 2)->default(0);
            $table->decimal('total_additions', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->decimal('total_inss', 15, 2)->default(0);
            $table->decimal('total_irt', 15, 2)->default(0);
            $table->decimal('total_net_paid', 15, 2)->default(0);
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_runs');
    }
};
