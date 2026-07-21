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

        // 1. Criar Permissões (Módulos Base do ERP)
        $permissions = [
            // Definições Globais e Administração
            'settings.view', 'settings.edit',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'companies.view', 'companies.create', 'companies.edit', 'companies.delete',
            'audits.view',
            
            // RH (Recursos Humanos)
            'hr.view', 'hr.create', 'hr.edit', 'hr.delete',
            
            // Armazéns e Stock (Inventário)
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete',
            
            // Compras
            'purchases.view', 'purchases.create', 'purchases.edit', 'purchases.delete', 'purchases.approve',
            
            // Vendas e Faturação
            'sales.view', 'sales.create', 'sales.edit', 'sales.cancel',
            
            // Tesouraria e Contas Correntes
            'treasury.view', 'treasury.create', 'treasury.edit', 'treasury.cancel',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Criar Papéis (Roles) e atribuir permissões existentes

        // Consulta (Read Only)
        $roleConsulta = Role::firstOrCreate(['name' => 'Consulta']);
        $roleConsulta->syncPermissions([
            'settings.view', 'users.view', 'roles.view', 'companies.view', 'audits.view',
            'hr.view', 'inventory.view', 'purchases.view', 'sales.view', 'treasury.view'
        ]);

        // Operador (Cria documentos, mas não edita configurações ou segurança)
        $roleOperador = Role::firstOrCreate(['name' => 'Operador']);
        $roleOperador->syncPermissions([
            'inventory.view', 'inventory.create',
            'purchases.view', 'purchases.create',
            'sales.view', 'sales.create',
            'treasury.view', 'treasury.create'
        ]);

        // Gestor (Gestão departamental, aprovações, edições, anulações)
        $roleGestor = Role::firstOrCreate(['name' => 'Gestor']);
        $roleGestor->syncPermissions([
            'hr.view', 'hr.create', 'hr.edit',
            'inventory.view', 'inventory.create', 'inventory.edit',
            'purchases.view', 'purchases.create', 'purchases.edit', 'purchases.approve',
            'sales.view', 'sales.create', 'sales.edit', 'sales.cancel',
            'treasury.view', 'treasury.create', 'treasury.edit', 'treasury.cancel'
        ]);

        // Administrador (Tudo exceto segurança avançada se desejado, mas aqui damos quase tudo)
        $roleAdmin = Role::firstOrCreate(['name' => 'Administrador']);
        $roleAdmin->syncPermissions(Permission::all());

        // Super Admin (Bypass total definido no Gate::before em AppServiceProvider)
        Role::firstOrCreate(['name' => 'Super Admin']);
    }
}
