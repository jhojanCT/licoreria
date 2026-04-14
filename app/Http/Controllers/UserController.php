<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('users.manage');

        $users = User::query()
            ->with('roles')
            ->orderBy('name')
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('users.manage');

        $roles = Role::query()->where('guard_name', 'web')->orderBy('name')->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('users.manage');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:32',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => ['required', Rule::exists('roles', 'id')->where('guard_name', 'web')],
        ]);

        $role = Role::findOrFail($validated['role_id']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([$role]);

        return redirect()->route('users.index')->with('success', 'Usuario creado. Ya puede iniciar sesión.');
    }

    public function edit(User $user): View
    {
        $this->authorize('users.manage');

        $roles = Role::query()->where('guard_name', 'web')->orderBy('name')->get();
        $user->load('roles');

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('users.manage');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:32',
            'role_id' => ['required', Rule::exists('roles', 'id')->where('guard_name', 'web')],
        ];
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules);

        $role = Role::findOrFail($validated['role_id']);

        if ($user->id === auth()->id()) {
            $perms = $role->permissions->pluck('name');
            if (! $perms->contains('users.manage')) {
                return back()->withErrors(['role_id' => 'No puede quitarse el rol de administrador a su propia cuenta.']);
            }
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => $validated['password']]);
        }

        $user->syncRoles([$role]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado.');
    }
}
