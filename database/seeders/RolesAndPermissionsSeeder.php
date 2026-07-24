<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Criar Permissões (Todos os Módulos do ERP Consulvolt)
        $permissions = [
            // Dashboard Global & Business Intelligence
            'dashboard.view', 'bi.view',
            
            // Logística & Inventário
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete',
            
            // Vendas & Faturação
            'sales.view', 'sales.create', 'sales.edit', 'sales.cancel', 'pos.access', 'saft.export',
            
            // Compras
            'purchases.view', 'purchases.create', 'purchases.edit', 'purchases.delete', 'purchases.approve',
            
            // Salários & RH
            'hr.view', 'hr.create', 'hr.edit', 'hr.delete', 'payroll.process', 'payroll.export',
            
            // Terceiros (Clientes & Fornecedores)
            'third_parties.view', 'third_parties.create', 'third_parties.edit', 'third_parties.delete',
            
            // Tesouraria & Contas Correntes
            'treasury.view', 'treasury.create', 'treasury.edit', 'treasury.cancel',
            
            // Ativos Fixos (Imobilizado)
            'assets.view', 'assets.create', 'assets.edit', 'assets.delete',
            
            // Contabilidade PGC
            'accounting.view', 'accounting.create', 'accounting.edit', 'accounting.delete',
            
            // SGD / Gestão Documental
            'documents.view', 'documents.manage',
            
            // Definições, Integrações e Administração
            'settings.view', 'settings.edit',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'companies.view', 'companies.create', 'companies.edit', 'companies.delete',
            'integrations.view', 'integrations.manage',
            'backups.manage', 'audits.view',
            'billing.view', 'billing.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Criar Papéis (Roles) e atribuir permissões por defeito

        // Operador POS (Acesso EXCLUSIVO ao POS e Vendas)
        $roleOperadorPos = Role::firstOrCreate(['name' => 'Operador POS']);
        $roleOperadorPos->syncPermissions(['pos.access', 'sales.create', 'sales.view']);

        // Operador Comercial (Vendas, Clientes, Guias e Orçamentos)
        $roleOperadorComercial = Role::firstOrCreate(['name' => 'Operador Comercial']);
        $roleOperadorComercial->syncPermissions([
            'dashboard.view', 'pos.access', 'sales.view', 'sales.create', 'sales.edit',
            'third_parties.view', 'third_parties.create', 'inventory.view'
        ]);

        // Gestor de Armazém / Stock
        $roleGestorStock = Role::firstOrCreate(['name' => 'Gestor de Armazém']);
        $roleGestorStock->syncPermissions([
            'dashboard.view', 'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete',
            'purchases.view', 'purchases.create', 'third_parties.view', 'pos.access'
        ]);

        // Técnico de RH (Gestão de Funcionários, Contratos e Salários)
        $roleTecnicoRh = Role::firstOrCreate(['name' => 'Técnico de RH']);
        $roleTecnicoRh->syncPermissions([
            'dashboard.view', 'hr.view', 'hr.create', 'hr.edit', 'hr.delete',
            'payroll.process', 'payroll.export', 'third_parties.view'
        ]);

        // Contabilista (Contabilidade PGC, Ativos, Tesouraria, Balancetes, BI)
        $roleContabilista = Role::firstOrCreate(['name' => 'Contabilista']);
        $roleContabilista->syncPermissions([
            'dashboard.view', 'bi.view', 'accounting.view', 'accounting.create', 'accounting.edit',
            'assets.view', 'assets.create', 'assets.edit',
            'treasury.view', 'treasury.create', 'treasury.edit',
            'sales.view', 'purchases.view', 'saft.export', 'third_parties.view', 'documents.view'
        ]);

        // Consulta / Read-Only (Apenas visualização geral)
        $roleConsulta = Role::firstOrCreate(['name' => 'Consulta']);
        $roleConsulta->syncPermissions([
            'dashboard.view', 'bi.view', 'settings.view', 'users.view', 'roles.view', 'companies.view', 'audits.view',
            'hr.view', 'inventory.view', 'purchases.view', 'sales.view', 'treasury.view', 'accounting.view', 'assets.view'
        ]);

        // Gestor (Supervisão departamental, edições, anulações e aprovações)
        $roleGestor = Role::firstOrCreate(['name' => 'Gestor']);
        $roleGestor->syncPermissions([
            'dashboard.view', 'bi.view',
            'hr.view', 'hr.create', 'hr.edit', 'payroll.process', 'payroll.export',
            'inventory.view', 'inventory.create', 'inventory.edit',
            'purchases.view', 'purchases.create', 'purchases.edit', 'purchases.approve',
            'sales.view', 'sales.create', 'sales.edit', 'sales.cancel', 'saft.export',
            'treasury.view', 'treasury.create', 'treasury.edit', 'treasury.cancel',
            'assets.view', 'assets.create', 'assets.edit',
            'accounting.view', 'third_parties.view', 'third_parties.create', 'third_parties.edit',
            'documents.view', 'documents.manage', 'pos.access'
        ]);

        // Administrador de Empresa (Acesso total à empresa ativa)
        $roleAdmin = Role::firstOrCreate(['name' => 'Administrador']);
        $roleAdmin->syncPermissions(Permission::all());

        // Super Admin (Bypass total do sistema)
        Role::firstOrCreate(['name' => 'Super Admin']);
    }
}
