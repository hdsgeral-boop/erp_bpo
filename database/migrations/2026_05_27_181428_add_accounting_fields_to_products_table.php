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
        Schema::table('products', function (Blueprint $table) {
            $table->string('account_cost')->nullable();
            $table->string('account_purchase')->nullable();
            $table->string('account_inventory')->nullable();
            $table->boolean('is_asset')->default(false);
            $table->boolean('is_blocked')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['account_cost', 'account_purchase', 'account_inventory', 'is_asset', 'is_blocked']);
        });
    }
};
