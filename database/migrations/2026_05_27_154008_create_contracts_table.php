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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('infotype_id')->constrained()->cascadeOnDelete();
            $table->decimal('value_per_day', 15, 2)->default(0);
            $table->integer('contract_days_month')->default(22);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status')->default('Ativo');
            $table->boolean('is_master_data')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
