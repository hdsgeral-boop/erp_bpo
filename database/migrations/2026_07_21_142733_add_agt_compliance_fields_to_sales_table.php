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
            $table->string('hash_control')->nullable()->after('hash')->comment('Versão da chave RSA da AGT');
            $table->string('agt_status')->nullable()->after('hash_control')->comment('Estado de Validação na AGT via Webhook');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['hash_control', 'agt_status']);
        });
    }
};
