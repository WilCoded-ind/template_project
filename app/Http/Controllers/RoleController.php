<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return $this->getDataTable();
        }

        return view('roles.index');
    }

    // Method private untuk memproses DataTable server-side
    private function getDataTable()
    {
        $query = Role::withCount(['users', 'permissions']);

        return DataTables::of($query)
            ->addIndexColumn()

            // Edit kolom untuk menampilkan jumlah users dan permissions
            ->editColumn('users_count', fn($role) => $role->users_count)
            ->editColumn('permissions_count', fn($role) => $role->permissions_count)

            // Tambah kolom action dengan tombol view, edit, delete
            ->addColumn('action', function ($role) {
                $actions = '<div class="flex justify-start gap-2">';

                $actions .= '<a href="' . route('roles.show', $role) . '" class="text-blue-600 hover:text-blue-900">Lihat</a>';

                if (auth()->user()->hasPermission('role.edit')) {
                    $actions .= '<a href="' . route('roles.edit', $role) . '" class="text-indigo-600 hover:text-indigo-900">Edit</a>';
                }

                if (auth()->user()->hasPermission('role.delete')) {
                    $actions .= '<button onclick="deleteRole(' . $role->id . ')" class="text-red-600 hover:text-red-900">Hapus</button>';
                }

                $actions .= '</div>';

                return $actions;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('roles.create', compact('permissions'));
    }

    // Menyimpan role baru ke database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles', 'alpha_dash'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
        ]);

        $role->permissions()->attach($validated['permissions']);

        return redirect()->route('roles.index')->with('success', 'Role berhasil dibuat.');
    }

    public function show(Role $role)
    {
        $role->load('permissions', 'users');

        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
        $role->load('permissions');

        return view('roles.edit', compact('role', 'permissions'));
    }

    // Update role ke database
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:roles,name,' . $role->id],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
        ]);

        $role->permissions()->sync($validated['permissions']);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    // Menghapus role dari database
    public function destroy(Role $role)
    {
        // Cek apakah role sedang digunakan oleh users
        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus role yang sedang digunakan oleh users.',
            ], 403);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dihapus.',
        ]);
    }

    // Export CSV (Opsional - jika diperlukan)
    public function export(Request $request)
    {
        $query = Role::withCount('users', 'permissions');

        // Apply filters jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%");
            });
        }

        $roles = $query->get();

        $filename = 'roles_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($roles) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Name', 'Display Name', 'Users Count', 'Permissions Count', 'Created At']);

            foreach ($roles as $role) {
                fputcsv($file, [
                    $role->name,
                    $role->display_name,
                    $role->users_count,
                    $role->permissions_count,
                    $role->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
