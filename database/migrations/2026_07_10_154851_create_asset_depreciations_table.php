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
        Schema::create('asset_depreciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_asset_id')->constrained('fixed_assets')->cascadeOnDelete();
            $table->integer('year');
            $table->integer('month')->nullable()->comment('Null para amortização anual');
            $table->decimal('depreciation_amount', 15, 2);
            $table->decimal('accumulated_amount', 15, 2);
            $table->decimal('net_book_value', 15, 2);
            $table->timestamp('processed_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['fixed_asset_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_depreciations');
    }
};
