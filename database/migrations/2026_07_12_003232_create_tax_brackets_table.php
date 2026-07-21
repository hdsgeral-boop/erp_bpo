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
        Schema::create('tax_brackets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('min_value', 15, 2);
            $table->decimal('max_value', 15, 2)->nullable(); // null means infinity
            $table->decimal('fixed_portion', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2); // percentage
            $table->decimal('excess_of', 15, 2)->default(0); // For IRT: (Salary - excess_of) * rate + fixed
            $table->date('valid_from');
            $table->date('valid_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_brackets');
    }
};
