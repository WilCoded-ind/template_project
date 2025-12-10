<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::latest()->paginate(20);
        return view('permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:permissions', 'regex:/^[a-z0-9._-]+$/'],
                'display_name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
            ]);

            Permission::create([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? null,
            ]);

            return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(Permission $permission)
    {
        $permission->load('roles');
        return view('permissions.show', compact('permission'));
    }

    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9._-]+$/', 'unique:permissions,name,' . $permission->id],
                'display_name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
            ]);

            $permission->update([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? null,
            ]);

            return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(Permission $permission)
    {
        if ($permission->roles()->count() > 0) {
            return redirect()->route('permissions.index')->with('error', 'Cannot delete permission that is assigned to roles.');
        }

        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
