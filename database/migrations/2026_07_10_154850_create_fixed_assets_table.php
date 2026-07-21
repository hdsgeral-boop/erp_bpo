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
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('asset_categories')->restrictOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->date('purchase_date');
            $table->decimal('purchase_value', 15, 2);
            $table->decimal('residual_value', 15, 2)->default(0);
            $table->integer('useful_life_years')->nullable();
            $table->enum('status', ['active', 'sold', 'written_off'])->default('active');
            $table->foreignId('vendor_id')->nullable()->constrained('third_parties')->nullOnDelete();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};
