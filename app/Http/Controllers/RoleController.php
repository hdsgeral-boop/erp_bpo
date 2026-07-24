<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Role::with('permissions');
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        $roles = $query->orderBy('name')->paginate(10);
        
            return view('admin.roles.index', compact('roles', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Group permissions by prefix (e.g., "users.create" -> "users")
        $permissions = Permission::all()->groupBy(function($perm) {
            $parts = explode('.', $perm->name);
            return count($parts) > 1 ? $parts[0] : 'geral';
        });

        return view('admin.roles.create', compact('permissions'));
    }

    /**
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
            
            if (!empty($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }

            // Limpar cache de permissões do Spatie para surtir efeito imediato
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            
            DB::commit();
            return redirect()->route('admin.roles.index')->with('success', 'Perfil criado com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar o perfil: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        
        $permissions = $role->permissions->groupBy(function($perm) {
            $parts = explode('.', $perm->name);
            return count($parts) > 1 ? $parts[0] : 'geral';
        });

        return view('admin.roles.show', compact('role', 'permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        
        $permissions = Permission::all()->groupBy(function($perm) {
            $parts = explode('.', $perm->name);
            return count($parts) > 1 ? $parts[0] : 'geral';
        });
        
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        DB::beginTransaction();
        try {
            // Se for o Super Admin (ID 1), impedir alteração de nome (ou bloqueio de permissions, se definido pela regra de negócio)
            if ($role->id === 1) {
                $role->syncPermissions(Permission::all()); // Super Admin tem sempre tudo
            } else {
                $role->name = $validated['name'];
                $role->save();
                
                if (isset($validated['permissions'])) {
                    $role->syncPermissions($validated['permissions']);
                } else {
                    $role->syncPermissions([]); // Remover todas
                }
            }

            // Limpar cache de permissões do Spatie para surtir efeito imediato
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            
            DB::commit();
            return redirect()->route('admin.roles.index')->with('success', 'Perfil atualizado com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar o perfil: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        
        if ($role->id === 1) {
            return back()->with('error', 'Não é possível eliminar o perfil de Administrador Principal.');
        }
        
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Não é possível eliminar este perfil, pois existem utilizadores associados.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Perfil eliminado com sucesso.');
    }
}
