<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('permission', 'parent', 'children')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        $permissions = Permission::all();
        $parentMenus = Menu::whereNull('parent_id')->orderBy('order')->get();
        return view('menus.create', compact('permissions', 'parentMenus'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:menus'],
                'display_name' => ['required', 'string', 'max:255'],
                'icon' => ['nullable', 'string', 'max:255'],
                'route' => ['nullable', 'string', 'max:255'],
                'url' => ['nullable', 'string', 'max:255'],
                'parent_id' => ['nullable', 'exists:menus,id'],
                'order' => ['required', 'integer', 'min:0'],
                'permission_id' => ['nullable', 'exists:permissions,id']
            ]);

            Menu::create([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'icon' => $validated['icon'] ?? null,
                'route' => $validated['route'] ?? null,
                'url' => $validated['url'] ?? null,
                'parent_id' => $validated['parent_id'] ?? null,
                'order' => $validated['order'],
                'is_active' => $request->has('is_active') ? true : false,
                'permission_id' => $validated['permission_id'] ?? null,
            ]);

            return redirect()->route('menus.index')->with('success', 'Menu created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(Menu $menu)
    {
        $menu->load('permission', 'parent', 'children');
        return view('menus.show', compact('menu'));
    }

    public function edit(Menu $menu)
    {
        $permissions = Permission::all();
        $parentMenus = Menu::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->orderBy('order')
            ->get();
        return view('menus.edit', compact('menu', 'permissions', 'parentMenus'));
    }

    public function update(Request $request, Menu $menu)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:menus,name,' . $menu->id],
                'display_name' => ['required', 'string', 'max:255'],
                'icon' => ['nullable', 'string', 'max:255'],
                'route' => ['nullable', 'string', 'max:255'],
                'url' => ['nullable', 'string', 'max:255'],
                'parent_id' => ['nullable', 'exists:menus,id'],
                'order' => ['required', 'integer', 'min:0'],
                'permission_id' => ['nullable', 'exists:permissions,id']
            ]);

            $menu->update([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'icon' => $validated['icon'] ?? null,
                'route' => $validated['route'] ?? null,
                'url' => $validated['url'] ?? null,
                'parent_id' => $validated['parent_id'] ?? null,
                'order' => $validated['order'],
                'is_active' => $request->has('is_active') ? true : false,
                'permission_id' => $validated['permission_id'] ?? null,
            ]);

            return redirect()->route('menus.index')->with('success', 'Menu updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(Menu $menu)
    {
        if ($menu->children()->count() > 0) {
            return redirect()->route('menus.index')->with('error', 'Cannot delete menu that has sub-menus.');
        }

        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menu deleted successfully.');
    }
}
