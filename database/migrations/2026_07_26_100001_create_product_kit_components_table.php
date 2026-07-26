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
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'is_kit')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('is_kit')->default(false)->after('is_inventory');
            });
        }

        Schema::create('product_kit_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('component_product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('quantity', 15, 2)->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_kit_components');

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'is_kit')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('is_kit');
            });
        }
    }
};
