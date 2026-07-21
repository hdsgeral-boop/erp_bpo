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
        Schema::create('journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->date('doc_date');
            $table->date('entry_date');
            $table->string('reference')->nullable();
            $table->string('doc_number');
            $table->decimal('value', 15, 2);
            $table->string('type_dc'); // D, C
            $table->string('account_code');
            $table->unsignedBigInteger('third_party_id')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('reconciliation_id')->nullable();
            $table->boolean('is_master_data')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_lines');
    }
};
