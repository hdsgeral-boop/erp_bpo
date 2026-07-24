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
            $table->text('nif')->nullable()->change();
            $table->text('inss')->nullable()->change();
            $table->text('iban')->nullable()->change();
            $table->text('address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('nif', 255)->nullable()->change();
            $table->string('inss', 255)->nullable()->change();
            $table->string('iban', 255)->nullable()->change();
        });
    }
};
