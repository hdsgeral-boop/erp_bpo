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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name'); // Ex: IVA 14%
            $table->string('code')->nullable(); // Ex: NOR, ISE
            $table->string('type')->default('VAT'); // VAT, RETENTION, STAMP
            $table->decimal('rate', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('exemption_reason')->nullable(); // Ex: M04 - Isento Artigo 9
            $table->text('observations')->nullable();
            
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
