<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    // Menampilkan daftar users dengan DataTable server-side
    public function index()
    {
        if (request()->ajax()) {
            return $this->getDataTable();
        }

        $roles = Role::all();

        return view('users.index', compact('roles'));
    }

    // Method private untuk memproses DataTable server-side
    private function getDataTable()
    {
        $query = User::with('roles')->select('users.*');

        return DataTables::of($query)
            ->addIndexColumn()
            // Tambah kolom checkbox untuk bulk actions
            ->addColumn('checkbox', function ($user) {
                if ($user->id === auth()->id()) {
                    return '<input type="checkbox" disabled class="border-gray-300 rounded">';
                }

                return '<input type="checkbox" name="user_ids[]" value="'.$user->id.'" class="border-gray-300 rounded user-checkbox">';
            })
            // Tampilkan roles dengan badge
            ->addColumn('roles', function ($user) {
                $roles = '';
                foreach ($user->roles as $role) {
                    $roles .= '<span class="inline-flex px-2 mb-1 mr-1 text-xs font-semibold leading-5 text-blue-800 bg-blue-100 rounded-full">'.
                        $role->display_name.
                        '</span>';
                }

                return $roles ?: '-';
            })
            // Tampilkan status user (aktif/tidak aktif)
            ->addColumn('status', function ($user) {
                if ($user->is_active) {
                    return '<span class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">Aktif</span>';
                }

                return '<span class="inline-flex px-2 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full">Tidak Aktif</span>';
            })
            // Tampilkan tanggal pembuatan user
            ->addColumn('created_date', function ($user) {
                return $user->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function ($user) {
                return view('components.user-actions', ['user' => $user])->render();
            })
            ->filter(function ($query) {
                // Pencarian berdasarkan nama atau username
                if (request()->has('search') && request('search')['value']) {
                    $search = request('search')['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    });
                }

                // Filter berdasarkan role
                if (request()->filled('role_filter')) {
                    $query->whereHas('roles', function ($q) {
                        $q->where('roles.id', request('role_filter'));
                    });
                }

                // Filter berdasarkan status
                if (request()->filled('status_filter')) {
                    $query->where('is_active', request('status_filter') === 'active' ? 1 : 0);
                }
            })
            ->rawColumns(['checkbox', 'roles', 'status', 'action'])
            ->make(true);
    }

    // Membuat user baru
    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    // Menyimpan user baru ke database
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', 'min:8'],
                'roles' => ['required', 'array', 'min:1'],
                'roles.*' => ['exists:roles,id'],
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            $user->roles()->attach($validated['roles']);

            return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Kesalahan: '.$e->getMessage());
        }
    }

    // Menampilkan detail user
    public function show(User $user)
    {
        $user->load('roles.permissions');

        return view('users.show', compact('user'));
    }

    // Halaman edit user
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');

        return view('users.edit', compact('user', 'roles'));
    }

    // Update user ke database
    public function update(Request $request, User $user)
    {
        try {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$user->id],
                'roles' => ['required', 'array', 'min:1'],
                'roles.*' => ['exists:roles,id'],
            ];

            if ($request->filled('password')) {
                $rules['password'] = ['required', 'confirmed', 'min:8'];
            }

            $validated = $request->validate($rules);

            $user->update([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            $user->roles()->sync($validated['roles']);

            return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Kesalahan: '.$e->getMessage());
        }
    }

    // Menghapus user dari database
    public function destroy(User $user)
    {
        // Cek apakah user menghapus akun sendiri
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak bisa menghapus akun Anda sendiri.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus.',
        ]);
    }

    // Bulk Actions - Aksi massal untuk users
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Hapus user ID sendiri dari array agar tidak menghapus akun sendiri
        $userIds = array_diff($request->user_ids, [auth()->id()]);

        if (empty($userIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada user yang dipilih.',
            ], 400);
        }

        switch ($request->action) {
            case 'delete':
                User::whereIn('id', $userIds)->delete();
                $message = count($userIds).' user berhasil dihapus.';
                break;
            case 'activate':
                User::whereIn('id', $userIds)->update(['is_active' => true]);
                $message = count($userIds).' user berhasil diaktifkan.';
                break;
            case 'deactivate':
                User::whereIn('id', $userIds)->update(['is_active' => false]);
                $message = count($userIds).' user berhasil dinonaktifkan.';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    // Export to CSV
    public function export(Request $request)
    {
        $query = User::with('roles');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_filter')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('roles.id', $request->role_filter);
            });
        }

        if ($request->filled('status_filter')) {
            $query->where('is_active', $request->status_filter === 'active' ? 1 : 0);
        }

        $users = $query->get();

        $filename = 'users_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Name', 'Username', 'Roles', 'Status', 'Created At']);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->username,
                    $user->roles->pluck('display_name')->implode(', '),
                    $user->is_active ? 'Active' : 'Inactive',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
