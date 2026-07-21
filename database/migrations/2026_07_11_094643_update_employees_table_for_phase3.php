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
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('department')->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->after('position')->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('status');
        });

        // Tentar migrar dados existentes (lógica simples baseada no nome)
        // Em produção pesada, este bloco deveria ser um script isolado.
        $employees = \Illuminate\Support\Facades\DB::table('employees')->get();
        foreach ($employees as $employee) {
            $updateData = [];

            if (!empty($employee->department)) {
                $dep = \Illuminate\Support\Facades\DB::table('departments')
                    ->where('name', $employee->department)
                    ->where('company_id', $employee->company_id)
                    ->first();
                if ($dep) {
                    $updateData['department_id'] = $dep->id;
                } else {
                    // Se não existe, cria um genérico para não perder dados
                    $depId = \Illuminate\Support\Facades\DB::table('departments')->insertGetId([
                        'company_id' => $employee->company_id,
                        'name' => $employee->department,
                        'code' => strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $employee->department), 0, 10)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $updateData['department_id'] = $depId;
                }
            }

            if (!empty($employee->position)) {
                $pos = \Illuminate\Support\Facades\DB::table('positions')
                    ->where('title', $employee->position)
                    ->where('department_id', $updateData['department_id'] ?? null)
                    ->first();
                if ($pos) {
                    $updateData['position_id'] = $pos->id;
                } else {
                    // Cria cargo genérico se não existir
                    $posId = \Illuminate\Support\Facades\DB::table('positions')->insertGetId([
                        'department_id' => $updateData['department_id'] ?? null,
                        'title' => $employee->position,
                        'code' => strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $employee->position), 0, 10)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $updateData['position_id'] = $posId;
                }
            }
            
            $updateData['is_active'] = ($employee->status === 'Ativo');

            if (!empty($updateData)) {
                \Illuminate\Support\Facades\DB::table('employees')
                    ->where('id', $employee->id)
                    ->update($updateData);
            }
        }

        // Remover as colunas de texto antigas
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['department', 'position', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('status')->default('Ativo');
        });
        
        $employees = \Illuminate\Support\Facades\DB::table('employees')->get();
        foreach ($employees as $employee) {
            $dep = $employee->department_id ? \Illuminate\Support\Facades\DB::table('departments')->where('id', $employee->department_id)->first() : null;
            $pos = $employee->position_id ? \Illuminate\Support\Facades\DB::table('positions')->where('id', $employee->position_id)->first() : null;
            
            \Illuminate\Support\Facades\DB::table('employees')
                ->where('id', $employee->id)
                ->update([
                    'department' => $dep ? $dep->name : null,
                    'position' => $pos ? $pos->title : null,
                    'status' => $employee->is_active ? 'Ativo' : 'Inativo',
                ]);
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['position_id']);
            $table->dropColumn(['department_id', 'position_id', 'is_active']);
        });
    }
};
