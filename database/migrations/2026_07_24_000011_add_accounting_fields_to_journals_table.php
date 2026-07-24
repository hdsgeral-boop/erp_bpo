<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            if (!Schema::hasColumn('journals', 'reference')) {
                $table->string('reference')->nullable()->after('description');
            }
            if (!Schema::hasColumn('journals', 'date')) {
                $table->date('date')->nullable()->after('reference');
            }
            if (!Schema::hasColumn('journals', 'total_debit')) {
                $table->decimal('total_debit', 15, 2)->default(0)->after('date');
            }
            if (!Schema::hasColumn('journals', 'total_credit')) {
                $table->decimal('total_credit', 15, 2)->default(0)->after('total_debit');
            }
            if (!Schema::hasColumn('journals', 'status')) {
                $table->string('status')->default('POSTED')->after('total_credit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn(['reference', 'date', 'total_debit', 'total_credit', 'status']);
        });
    }
};
