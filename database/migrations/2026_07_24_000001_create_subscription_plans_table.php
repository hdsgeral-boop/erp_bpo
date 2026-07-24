<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->decimal('price_monthly', 12, 2);
            $table->integer('max_users')->default(3); // -1 = unlimited
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed os 3 planos padrão com os preços exatos solicitados pelo utilizador
        DB::table('subscription_plans')->insert([
            [
                'code' => 'start',
                'name' => 'Plano Start (PME)',
                'price_monthly' => 5000.00,
                'max_users' => 3,
                'features' => json_encode(['pos' => true, 'sales' => true, 'saft' => true, 'payroll' => false, 'accounting' => false, 'powerbi' => false, 'ai' => false, 'multi_company' => false]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'pro',
                'name' => 'Plano Pro (Empresarial)',
                'price_monthly' => 8500.00,
                'max_users' => 10,
                'features' => json_encode(['pos' => true, 'sales' => true, 'saft' => true, 'payroll' => true, 'accounting' => true, 'powerbi' => false, 'ai' => false, 'multi_company' => false]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'enterprise',
                'name' => 'Plano Enterprise (Corporativo)',
                'price_monthly' => 12799.00,
                'max_users' => -1,
                'features' => json_encode(['pos' => true, 'sales' => true, 'saft' => true, 'payroll' => true, 'accounting' => true, 'powerbi' => true, 'ai' => true, 'multi_company' => true]),
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
