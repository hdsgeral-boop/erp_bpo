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
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('name');
            }
            if (!Schema::hasColumn('companies', 'iban')) {
                $table->string('iban')->nullable()->after('address');
            }
            if (!Schema::hasColumn('companies', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('iban');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'iban', 'bank_name']);
        });
    }
};
