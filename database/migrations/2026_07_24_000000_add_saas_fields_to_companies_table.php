<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->timestamp('trial_ends_at')->nullable()->after('is_master_data');
            $table->string('subscription_status')->default('trial')->after('trial_ends_at'); // trial, active, expired, cancelled
            $table->timestamp('subscription_ends_at')->nullable()->after('subscription_status');
            $table->unsignedBigInteger('current_plan_id')->nullable()->after('subscription_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['trial_ends_at', 'subscription_status', 'subscription_ends_at', 'current_plan_id']);
        });
    }
};
