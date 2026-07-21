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
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('depreciation_rate', 5, 2)->comment('Taxa anual em %');
            $table->string('account_asset')->nullable()->comment('Conta SNC principal, ex: 43');
            $table->string('account_depreciation')->nullable()->comment('Conta SNC amort. acumulada, ex: 438');
            $table->string('account_expense')->nullable()->comment('Conta SNC gastos, ex: 642');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};
