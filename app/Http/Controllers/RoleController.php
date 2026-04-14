<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $this->authorize('roles.manage');

        $roles = Role::query()
            ->withCount('permissions')
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $this->authorize('roles.manage');

        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('roles.manage');

        $validated = $request->validate([
            'name' => 'required|string|max:64|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
        $role->syncPermissions(
            Permission::whereIn('id', $validated['permissions'] ?? [])->pluck('name')
        );

        return redirect()->route('roles.index')->with('success', 'Rol creado.');
    }

    public function edit(Role $role): View
    {
        $this->authorize('roles.manage');

        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();
        $role->load('permissions');

        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->authorize('roles.manage');

        $validated = $request->validate([
            'name' => 'required|string|max:64|unique:roles,name,'.$role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions(
            Permission::whereIn('id', $validated['permissions'] ?? [])->pluck('name')
        );

        return redirect()->route('roles.index')->with('success', 'Rol actualizado.');
    }
}
