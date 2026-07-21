<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = User::with(['roles', 'companies']);
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
        
        $users = $query->orderBy('name')->paginate(10);
        return view('admin.users.index', compact('users', 'search'));
    }

    public function create()
    {
        $roles = Role::all();
        $companies = Company::all();
        return view('admin.users.create', compact('roles', 'companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'companies' => 'array'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $role = Role::findById($validated['role_id']);
        $user->assignRole($role);

        if (isset($validated['companies'])) {
            $user->companies()->sync($validated['companies']);
        }

        return redirect()->route('admin.users.index')->with('success', 'Utilizador criado com sucesso.');
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $companies = Company::all();
        return view('admin.users.edit', compact('user', 'roles', 'companies'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role_id' => 'required|exists:roles,id',
            'companies' => 'array'
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $role = Role::findById($validated['role_id']);
        $user->syncRoles([$role]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if (isset($validated['companies'])) {
            $user->companies()->sync($validated['companies']);
        } else {
            $user->companies()->detach();
        }

        return redirect()->route('admin.users.index')->with('success', 'Utilizador atualizado.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Não pode eliminar o seu próprio utilizador.');
        }
        
        $user->companies()->detach();
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilizador eliminado com sucesso.');
    }
}
