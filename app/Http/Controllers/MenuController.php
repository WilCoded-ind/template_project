<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MenuController extends Controller
{
    // Menampilkan daftar menu dengan DataTable server-side
    public function index()
    {
        if (request()->ajax()) {
            return $this->getDataTable();
        }

        return view('menus.index');
    }

    // Method private untuk memproses DataTable server-side (termasuk parent dan children)
    private function getDataTable()
    {
        // Ambil semua menu (parent dan children) dengan relasi, urutkan by parent_id kemudian order
        $query = Menu::with('permission')
            ->orderBy('parent_id')
            ->orderBy('order');

        return DataTables::of($query)
            ->addIndexColumn()

            // Edit kolom untuk menampilkan nama menu dengan indentasi untuk children
            ->editColumn('display_name', function ($menu) {
                $indent = '';
                if ($menu->parent_id !== null) {
                    $indent = '<i class="fas fa-angle-right mr-2 text-gray-400" style="margin-left: 20px;"></i>';
                }

                return $indent.$menu->display_name;
            })

            // Edit kolom icon
            ->editColumn('icon', function ($menu) {
                if ($menu->icon) {
                    return '<i class="'.$menu->icon.'"></i>';
                }

                return '-';
            })

            // Edit kolom route/url
            ->editColumn('route_url', function ($menu) {
                return $menu->route ?? $menu->url ?? '-';
            })

            // Edit kolom permission
            ->editColumn('permission_name', function ($menu) {
                if ($menu->permission) {
                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">'.$menu->permission->display_name.'</span>';
                }

                return '-';
            })

            // Edit kolom order
            ->editColumn('order', fn ($menu) => $menu->order)

            // Edit kolom status
            ->editColumn('is_active', function ($menu) {
                if ($menu->is_active) {
                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>';
                }

                return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Tidak Aktif</span>';
            })

            // Tambah kolom action dengan tombol view, edit, delete
            ->addColumn('action', function ($menu) {
                return view('components.menu-actions', ['menu' => $menu])->render();
            })
            ->rawColumns(['action', 'display_name', 'icon', 'permission_name', 'is_active'])
            ->make(true);
    }

    // Membuat menu baru
    public function create()
    {
        $permissions = Permission::all();
        $parentMenus = Menu::whereNull('parent_id')->orderBy('order')->get();

        return view('menus.create', compact('permissions', 'parentMenus'));
    }

    // Menyimpan menu baru ke database
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
                'permission_id' => ['nullable', 'exists:permissions,id'],
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

            return redirect()->route('menus.index')->with('success', 'Menu berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Kesalahan: '.$e->getMessage());
        }
    }

    // Menampilkan detail menu
    public function show(Menu $menu)
    {
        $menu->load('permission', 'parent', 'children');

        return view('menus.show', compact('menu'));
    }

    // Halaman edit menu
    public function edit(Menu $menu)
    {
        $permissions = Permission::all();
        $parentMenus = Menu::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->orderBy('order')
            ->get();

        return view('menus.edit', compact('menu', 'permissions', 'parentMenus'));
    }

    // Update menu ke database
    public function update(Request $request, Menu $menu)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:menus,name,'.$menu->id],
                'display_name' => ['required', 'string', 'max:255'],
                'icon' => ['nullable', 'string', 'max:255'],
                'route' => ['nullable', 'string', 'max:255'],
                'url' => ['nullable', 'string', 'max:255'],
                'parent_id' => ['nullable', 'exists:menus,id'],
                'order' => ['required', 'integer', 'min:0'],
                'permission_id' => ['nullable', 'exists:permissions,id'],
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

            return redirect()->route('menus.index')->with('success', 'Menu berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Kesalahan: '.$e->getMessage());
        }
    }

    // Menghapus menu dari database
    public function destroy(Menu $menu)
    {
        // Cek apakah menu memiliki sub-menu
        if ($menu->children()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus menu yang memiliki sub-menu.',
            ], 403);
        }

        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil dihapus.',
        ]);
    }
}
