<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Exibe a vista Gerir Utilizadores (Hierárquica por Empresa para Super Admin & Limitada para Admins de Empresa).
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user() ?? User::first() ?? new User(['id' => 1, 'name' => 'Super Admin']);
        $isSuperAdmin = $currentUser->id === 1 || ($currentUser->exists && ($currentUser->hasRole('Super Admin') || $currentUser->roles->pluck('name')->contains('Super Admin')));

        $search = $request->input('search');
        $companyFilter = $request->input('company_id');

        // Se for Super Admin, carrega a estrutura hierárquica Empresa >> Utilizadores
        if ($isSuperAdmin) {
            $companiesQuery = Company::with(['users' => function($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                }
                $q->with('roles');
            }]);

            if ($companyFilter) {
                $companiesQuery->where('id', $companyFilter);
            }

            $companies = $companiesQuery->orderBy('name')->get();

            // Utilizadores globais sem empresa vinculada
            $unassignedUsersQuery = User::doesntHave('companies')->with('roles');
            if ($search) {
                $unassignedUsersQuery->where('name', 'like', "%{$search}%")
                                     ->orWhere('email', 'like', "%{$search}%");
            }
            $unassignedUsers = $unassignedUsersQuery->get();

            $allCompanies = Company::all();

            return view('admin.users.index', compact(
                'companies',
                'unassignedUsers',
                'allCompanies',
                'isSuperAdmin',
                'search',
                'companyFilter'
            ));
        }

        // Se for Gestor/Admin da Empresa, restringe apenas aos utilizadores da sua empresa
        $userCompanyIds = $currentUser->companies ? $currentUser->companies->pluck('id')->toArray() : [];
        if (empty($userCompanyIds)) {
            $userCompanyIds = [session('company_id') ?? 1];
        }

        $usersQuery = User::whereHas('companies', function($q) use ($userCompanyIds) {
            $q->whereIn('companies.id', $userCompanyIds);
        })->with(['roles', 'companies']);

        if ($search) {
            $usersQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->orderBy('name')->paginate(12);
        $myCompany = Company::find($userCompanyIds[0] ?? 1);

        $companyFilter = null;
        return view('admin.users.index', compact(
            'users',
            'myCompany',
            'isSuperAdmin',
            'search',
            'companyFilter'
        ));
    }

    public function create()
    {
        $currentUser = auth()->user() ?? User::first() ?? new User(['id' => 1]);
        $isSuperAdmin = $currentUser->id === 1 || ($currentUser->exists && ($currentUser->hasRole('Super Admin') || $currentUser->roles->pluck('name')->contains('Super Admin')));

        $roles = Role::all();
        
        if ($isSuperAdmin) {
            $companies = Company::all();
        } else {
            $companies = $currentUser->companies ?? Company::all();
            if ($companies->isEmpty()) {
                $companies = Company::where('id', session('company_id') ?? 1)->get();
            }
        }

        return view('admin.users.create', compact('roles', 'companies', 'isSuperAdmin'));
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user() ?? User::first();
        $isSuperAdmin = $currentUser ? ($currentUser->id === 1 || ($currentUser->exists && ($currentUser->hasRole('Super Admin') || $currentUser->roles->pluck('name')->contains('Super Admin')))) : true;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'companies' => 'array'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'department' => $validated['department'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        $role = Role::findById($validated['role_id']);
        $user->assignRole($role);

        // Se for Super Admin, associa as empresas selecionadas
        if ($isSuperAdmin && !empty($validated['companies'])) {
            $user->companies()->sync($validated['companies']);
        } else {
            // Se for Admin de empresa, associa automaticamente à sua empresa ativa
            $myCompanyId = session('company_id') ?? ($currentUser->companies->first()?->id ?? 1);
            $user->companies()->sync([$myCompanyId]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Utilizador criado com sucesso.');
    }

    public function edit(string $id)
    {
        $currentUser = auth()->user() ?? User::first() ?? new User(['id' => 1]);
        $isSuperAdmin = $currentUser->id === 1 || ($currentUser->exists && ($currentUser->hasRole('Super Admin') || $currentUser->roles->pluck('name')->contains('Super Admin')));

        $user = User::with(['roles', 'companies'])->findOrFail($id);

        $roles = Role::all();
        $companies = $isSuperAdmin ? Company::all() : ($currentUser->companies ?? Company::all());

        return view('admin.users.edit', compact('user', 'roles', 'companies', 'isSuperAdmin'));
    }

    public function update(Request $request, string $id)
    {
        $currentUser = auth()->user() ?? User::first();
        $isSuperAdmin = $currentUser ? ($currentUser->id === 1 || ($currentUser->exists && ($currentUser->hasRole('Super Admin') || $currentUser->roles->pluck('name')->contains('Super Admin')))) : true;

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:30',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'role_id' => 'required|exists:roles,id',
            'companies' => 'array'
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'department' => $validated['department'] ?? null,
        ]);

        $role = Role::findById($validated['role_id']);
        $user->syncRoles([$role]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($isSuperAdmin && isset($validated['companies'])) {
            $user->companies()->sync($validated['companies']);
        }

        return redirect()->route('admin.users.index')->with('success', 'Utilizador atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Não pode eliminar a sua própria conta.');
        }

        $user->companies()->detach();
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilizador eliminado com sucesso.');
    }
}
