<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['PROVENTO', 'DESCONTO']);
            $table->enum('nature', ['FIXED', 'PERCENTAGE', 'FORMULA']);
            $table->decimal('fixed_value', 15, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->text('formula')->nullable();
            $table->integer('calculation_order')->default(100);
            $table->boolean('is_subject_to_irt')->default(true);
            $table->boolean('is_subject_to_inss')->default(true);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};
