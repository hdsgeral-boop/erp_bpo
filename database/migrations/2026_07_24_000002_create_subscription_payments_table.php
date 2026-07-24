<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('subscription_plans');
            $table->string('reference_code')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method'); // multicaixa_ref, express, transfer
            $table->json('payment_details')->nullable(); // Entidade, Referência, Telemóvel, IBAN
            $table->string('proof_attachment')->nullable(); // Anexo comprovativo transferência
            $table->string('status')->default('PENDING'); // PENDING, APPROVED, REJECTED
            $table->unsignedBigInteger('validated_by')->nullable(); // SuperAdmin or BackOffice User ID
            $table->timestamp('validated_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable(); // Link para fatura AGT gerada
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
