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
        Schema::create('treasury_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // PG (Pagamento), RC (Recebimento)
            $table->date('doc_date');
            $table->string('account_fin');
            $table->string('description')->nullable();
            $table->decimal('total_value', 15, 2);
            $table->string('status')->default('DRAFT');
            $table->string('reference')->nullable();
            $table->boolean('is_master_data')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treasury_documents');
    }
};
