<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;

class UserAndCompanySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Garantir que as Roles existem
        $this->call(RolesAndPermissionsSeeder::class);

        $roleSuperAdmin = Role::where('name', 'Super Admin')->first();
        $roleAdmin      = Role::where('name', 'Administrador')->first();
        $roleOperadorPos= Role::where('name', 'Operador POS')->first();

        // 2. Criar / Garantir Empresa 1: WSTB
        $wstb = Company::where('name', 'like', '%WSTB%')->orWhere('nif', '5418920192')->first();
        if (!$wstb) {
            $wstb = Company::create([
                'name' => 'WSTB - Consulvolt, Lda',
                'nif' => '5418920192',
                'province' => 'Luanda',
                'municipality' => 'Belas',
                'commune' => 'Talatona',
                'is_master_data' => false
            ]);
        }

        // 3. Criar / Garantir Empresa 2: Spazio
        $spazio = Company::where('name', 'like', '%Spazio%')->orWhere('nif', '5419820381')->first();
        if (!$spazio) {
            $spazio = Company::create([
                'name' => 'Spazio - Spazio Design & Serviços, Lda',
                'nif' => '5419820381',
                'province' => 'Luanda',
                'municipality' => 'Luanda',
                'commune' => 'Maianga',
                'is_master_data' => false
            ]);
        }

        // 4. Criar Utilizador 1: admin (Acesso total a todo o sistema e todas as empresas)
        $admin = User::where('email', 'admin@consulvolt.com')->orWhere('name', 'admin')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin Geral',
                'email' => 'admin@consulvolt.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }
        $admin->assignRole($roleSuperAdmin);

        // Associar admin a todas as empresas
        $allCompanies = Company::all();
        $admin->companies()->sync($allCompanies->pluck('id')->toArray());

        // 5. Criar Utilizador 2: celso.baptista@consulvolt.com (Admin das empresas WSTB e Spazio)
        $celso = User::where('email', 'celso.baptista@consulvolt.com')->first();
        if (!$celso) {
            $celso = User::create([
                'name' => 'Celso Baptista',
                'email' => 'celso.baptista@consulvolt.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }
        $celso->assignRole($roleAdmin);
        $celso->companies()->sync([$wstb->id, $spazio->id]);

        // 6. Criar Utilizador 3: leonel.caculo@consulvolt.com (Operador com acesso apenas ao POS)
        $leonel = User::where('email', 'leonel.caculo@consulvolt.com')->first();
        if (!$leonel) {
            $leonel = User::create([
                'name' => 'Leonel Caculo',
                'email' => 'leonel.caculo@consulvolt.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }
        $leonel->syncRoles([$roleOperadorPos]);
        $leonel->companies()->sync([$wstb->id]);

        echo "Seeder UserAndCompanySeeder concluído com sucesso:\n";
        echo "   - Empresa WSTB (ID: {$wstb->id})\n";
        echo "   - Empresa Spazio (ID: {$spazio->id})\n";
        echo "   - Utilizador admin@consulvolt.com (Super Admin)\n";
        echo "   - Utilizador celso.baptista@consulvolt.com (Admin WSTB & Spazio)\n";
        echo "   - Utilizador leonel.caculo@consulvolt.com (Operador POS)\n";
    }
}
