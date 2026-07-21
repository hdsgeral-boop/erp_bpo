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
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            
            $table->decimal('total_tax', 15, 2)->default(0)->after('total_amount');
            $table->decimal('total_discount', 15, 2)->default(0)->after('total_tax');
            
            $table->text('notes')->nullable();
            
            // Campos de anulação
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->default(0)->after('unit_price');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('tax_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['tax_rate', 'discount_amount']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['cancelled_by']);
            
            $table->dropColumn([
                'created_by', 'warehouse_id', 'total_tax', 'total_discount', 
                'notes', 'cancelled_by', 'cancelled_at', 'cancellation_reason'
            ]);
        });
    }
};
