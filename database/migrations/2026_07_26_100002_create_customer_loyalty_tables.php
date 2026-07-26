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
        if (Schema::hasTable('third_parties')) {
            Schema::table('third_parties', function (Blueprint $table) {
                if (!Schema::hasColumn('third_parties', 'loyalty_points')) {
                    $table->integer('loyalty_points')->default(0)->after('is_customer');
                }
                if (!Schema::hasColumn('third_parties', 'loyalty_tier')) {
                    $table->string('loyalty_tier')->default('BRONZE')->after('loyalty_points');
                }
            });
        }

        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('third_party_id')->constrained('third_parties')->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->string('type'); // EARN, REDEEM, EXPIRE
            $table->integer('points');
            $table->decimal('amount_kwanza', 15, 2)->default(0);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');

        if (Schema::hasTable('third_parties')) {
            Schema::table('third_parties', function (Blueprint $table) {
                if (Schema::hasColumn('third_parties', 'loyalty_points')) {
                    $table->dropColumn('loyalty_points');
                }
                if (Schema::hasColumn('third_parties', 'loyalty_tier')) {
                    $table->dropColumn('loyalty_tier');
                }
            });
        }
    }
};
