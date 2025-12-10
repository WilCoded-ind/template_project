<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    // Menampilkan daftar permission dengan DataTable server-side
    public function index()
    {
        if (request()->ajax()) {
            return $this->getDataTable();
        }

        return view('permissions.index');
    }

    // Method private untuk memproses DataTable server-side
    private function getDataTable()
    {
        $query = Permission::query();

        return DataTables::of($query)
            ->addIndexColumn()

            // Edit kolom untuk menampilkan data dengan format yang diinginkan
            ->editColumn('name', fn($permission) => $permission->name)
            ->editColumn('display_name', fn($permission) => $permission->display_name)
            ->editColumn('description', fn($permission) => $permission->description ?? '-')

            // Tambah kolom action dengan tombol view, edit, delete
            ->addColumn('action', function ($permission) {
                $actions = '<div class="flex justify-start gap-2">';

                $actions .= '<a href="' . route('permissions.show', $permission) . '" class="text-blue-600 hover:text-blue-900">View</a>';

                if (auth()->user()->hasPermission('permission.edit')) {
                    $actions .= '<a href="' . route('permissions.edit', $permission) . '" class="text-indigo-600 hover:text-indigo-900">Edit</a>';
                }

                if (auth()->user()->hasPermission('permission.delete')) {
                    $actions .= '<button onclick="deletePermission(' . $permission->id . ')" class="text-red-600 hover:text-red-900">Delete</button>';
                }

                $actions .= '</div>';

                return $actions;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // Membuat permission baru
    public function create()
    {
        return view('permissions.create');
    }

    // Menyimpan permission baru ke database
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

            return redirect()->route('permissions.index')->with('success', 'Permission berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }

    // Menampilkan detail permission
    public function show(Permission $permission)
    {
        $permission->load('roles');
        return view('permissions.show', compact('permission'));
    }

    // Halaman edit permission
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    // Update permission ke database
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

            return redirect()->route('permissions.index')->with('success', 'Permission berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }

    // Menghapus permission dari database
    public function destroy(Permission $permission)
    {
        // Cek apakah permission sedang digunakan oleh role
        if ($permission->roles()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus permission yang sedang digunakan oleh role.',
            ], 403);
        }

        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil dihapus.',
        ]);
    }
}
