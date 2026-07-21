<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Criar tabela de Contas de Tesouraria (Bancos e Caixas)
        Schema::create('treasury_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Ex: BAI AOA, Caixa Geral
            $table->string('currency')->default('AOA');
            $table->decimal('initial_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Atualizar Vendas (Faturas)
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('amount_paid', 15, 2)->default(0)->after('total_amount');
            $table->enum('payment_status', ['PENDING', 'PARTIAL', 'PAID'])->default('PENDING')->after('amount_paid');
        });

        // 3. Atualizar Compras (Faturas de Fornecedor)
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->decimal('amount_paid', 15, 2)->default(0)->after('total_amount');
            $table->enum('payment_status', ['PENDING', 'PARTIAL', 'PAID'])->default('PENDING')->after('amount_paid');
        });

        // 4. Adaptar Tabela de Recibos para lidar com Recibos e Pagamentos (Doc Genérico de Tesouraria)
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->renameColumn('customer_id', 'third_party_id');
            $table->renameColumn('receipt_number', 'doc_number');
            $table->string('doc_type')->default('RC')->after('company_id'); // RC = Recibo, PG = Pagamento
            $table->string('status')->default('ISSUED')->after('is_posted');
            $table->foreignId('treasury_account_id')->nullable()->after('payment_method')->constrained('treasury_accounts')->nullOnDelete();
        });
        
        Schema::table('receipts', function (Blueprint $table) {
            $table->foreign('third_party_id')->references('id')->on('third_parties')->cascadeOnDelete();
        });

        // 5. Adaptar Linhas de Recibo para ligar a Compras também
        Schema::table('receipt_items', function (Blueprint $table) {
            $table->dropForeign(['sale_id']);
            $table->unsignedBigInteger('sale_id')->nullable()->change();
            $table->foreignId('purchase_invoice_id')->nullable()->after('sale_id')->constrained('purchase_invoices')->cascadeOnDelete();
        });
        
        Schema::table('receipt_items', function (Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Reversão
    }
};
