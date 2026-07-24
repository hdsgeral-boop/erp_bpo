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
        Schema::table('pos_registers', function (Blueprint $table) {
            $table->string('printer_type')->nullable()->after('status')->comment('Ex: network, usb, bluetooth, browser');
            $table->string('printer_address')->nullable()->after('printer_type')->comment('IP, MAC, or printer name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_registers', function (Blueprint $table) {
            $table->dropColumn(['printer_type', 'printer_address']);
        });
    }
};
