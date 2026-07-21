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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('iban')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->date('admission_date')->nullable();
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->decimal('subsidy_meal', 15, 2)->default(0);
            $table->decimal('subsidy_transport', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'email', 'phone', 'address', 'bank_name', 'iban', 
                'department', 'position', 'admission_date', 
                'base_salary', 'subsidy_meal', 'subsidy_transport'
            ]);
        });
    }
};
