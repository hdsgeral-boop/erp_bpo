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
        Schema::create('document_series', function (Blueprint $table) {
            $table->id();
            $table->string('document_type'); // Ex: Fatura, Recibo, Encomenda
            $table->string('identifier'); // Ex: 2024, A, FS
            $table->string('description')->nullable();
            $table->integer('current_number')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
            
            // Uma série deve ser única para um tipo de documento numa empresa
            $table->unique(['document_type', 'identifier', 'company_id'], 'doc_series_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_series');
    }
};
