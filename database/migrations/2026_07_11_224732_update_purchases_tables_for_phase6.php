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
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            // Optional: reference to the converted order
            $table->foreignId('converted_to_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->text('notes')->nullable();
        });

        Schema::table('purchase_deliveries', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('delivery_note_number')->nullable();
            $table->text('notes')->nullable();
            // Fix warehouse_id to be a constrained foreign key if possible (it was an unsignedBigInteger previously)
            // But we will just add foreign key constraint if it doesn't exist
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_deliveries', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['created_by', 'delivery_note_number', 'notes']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'approved_by', 'approved_at', 'total_amount', 'total_tax', 'notes']);
        });

        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropForeign(['converted_to_order_id']);
            $table->dropColumn(['department_id', 'created_by', 'approved_by', 'approved_at', 'notes', 'converted_to_order_id']);
        });
    }
};
