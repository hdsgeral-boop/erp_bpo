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
        Schema::table('third_parties', function (Blueprint $table) {
            $table->dropColumn('type'); // Removes the old CUSTOMER/SUPPLIER enum/string column
            $table->boolean('is_customer')->default(false)->after('name');
            $table->boolean('is_supplier')->default(false)->after('is_customer');
            $table->string('postal_code')->nullable()->after('address');
            $table->string('city')->nullable()->after('postal_code');
            $table->string('country')->default('Angola')->after('city');
            $table->string('website')->nullable()->after('phone');
            $table->text('observations')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('third_parties', function (Blueprint $table) {
            $table->string('type')->default('CUSTOMER');
            $table->dropColumn([
                'is_customer',
                'is_supplier',
                'postal_code',
                'city',
                'country',
                'website',
                'observations',
                'is_active'
            ]);
        });
    }
};
