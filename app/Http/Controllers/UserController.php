<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return $this->getDataTable();
        }

        $roles = Role::all();
        return view('users.index', compact('roles'));
    }

    private function getDataTable()
    {
        $query = User::with('roles')->select('users.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($user) {
                if ($user->id === auth()->id()) {
                    return '<input type="checkbox" disabled class="rounded border-gray-300">';
                }
                return '<input type="checkbox" name="user_ids[]" value="' . $user->id . '" class="user-checkbox rounded border-gray-300">';
            })
            ->addColumn('roles', function ($user) {
                $roles = '';
                foreach ($user->roles as $role) {
                    $roles .= '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mr-1 mb-1">' . 
                              $role->display_name . 
                              '</span>';
                }
                return $roles ?: '-';
            })
            ->addColumn('status', function ($user) {
                if ($user->is_active) {
                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>';
                }
                return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>';
            })
            ->addColumn('created_date', function ($user) {
                return $user->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function ($user) {
                $actions = '<div class="flex gap-2">';
                
                $actions .= '<a href="' . route('users.show', $user) . '" class="text-blue-600 hover:text-blue-900">View</a>';
                
                if (auth()->user()->hasPermission('user.edit')) {
                    $actions .= '<a href="' . route('users.edit', $user) . '" class="text-indigo-600 hover:text-indigo-900">Edit</a>';
                }
                
                if (auth()->user()->hasPermission('user.delete') && $user->id !== auth()->id()) {
                    $actions .= '<button onclick="deleteUser(' . $user->id . ')" class="text-red-600 hover:text-red-900">Delete</button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->filter(function ($query) {
                // Search
                if (request()->has('search') && request('search')['value']) {
                    $search = request('search')['value'];
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('username', 'like', "%{$search}%");
                    });
                }

                // Filter by role
                if (request()->filled('role_filter')) {
                    $query->whereHas('roles', function($q) {
                        $q->where('roles.id', request('role_filter'));
                    });
                }

                // Filter by status
                if (request()->filled('status_filter')) {
                    $query->where('is_active', request('status_filter') === 'active' ? 1 : 0);
                }
            })
            ->rawColumns(['checkbox', 'roles', 'status', 'action'])
            ->make(true);
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', 'min:8'],
                'roles' => ['required', 'array', 'min:1'],
                'roles.*' => ['exists:roles,id']
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            $user->roles()->attach($validated['roles']);

            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        $user->load('roles.permissions');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        try {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
                'roles' => ['required', 'array', 'min:1'],
                'roles.*' => ['exists:roles,id']
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

            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 403);
        }

        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.'
        ]);
    }

    // Bulk Actions
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $userIds = array_diff($request->user_ids, [auth()->id()]);

        if (empty($userIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid users selected.'
            ], 400);
        }

        switch ($request->action) {
            case 'delete':
                User::whereIn('id', $userIds)->delete();
                $message = count($userIds) . ' user(s) deleted successfully.';
                break;
            case 'activate':
                User::whereIn('id', $userIds)->update(['is_active' => true]);
                $message = count($userIds) . ' user(s) activated successfully.';
                break;
            case 'deactivate':
                User::whereIn('id', $userIds)->update(['is_active' => false]);
                $message = count($userIds) . ' user(s) deactivated successfully.';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    // Export to CSV
    public function export(Request $request)
    {
        $query = User::with('roles');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_filter')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('roles.id', $request->role_filter);
            });
        }

        if ($request->filled('status_filter')) {
            $query->where('is_active', $request->status_filter === 'active' ? 1 : 0);
        }

        $users = $query->get();

        $filename = "users_" . date('Y-m-d_His') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Name', 'Username', 'Roles', 'Status', 'Created At']);
            
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->username,
                    $user->roles->pluck('display_name')->implode(', '),
                    $user->is_active ? 'Active' : 'Inactive',
                    $user->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}