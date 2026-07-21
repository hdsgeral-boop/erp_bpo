<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_payroll_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // Ex: ITEM, INSS_EMP, INSS_COMP, IRT, NET_PAY
            $table->foreignId('payroll_item_id')->nullable()->constrained('payroll_items')->cascadeOnDelete();
            $table->string('debit_account')->nullable();
            $table->string('credit_account')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_payroll_maps');
    }
};
