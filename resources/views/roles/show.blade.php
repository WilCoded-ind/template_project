<x-adminlte-layout>
    <div>
        <div class="flex items-center justify-between">
            <h1 class="my-1 text-3xl font-semibold text-gray-900">{{ __('Role Details') }}</h1>
            <div class="flex gap-2">
                @if (auth()->user()->hasPermission('role.edit'))
                    <a href="{{ route('roles.edit', $role) }}"
                        class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                        Edit Role
                    </a>
                @endif
                <a href="{{ route('roles.index') }}"
                    class="px-4 py-2 font-bold text-white bg-gray-500 rounded hover:bg-gray-700">
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="mb-4 text-lg font-semibold">Role Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Name</p>
                            <p class="mt-1">{{ $role->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Display Name</p>
                            <p class="mt-1">{{ $role->display_name }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-500">Description</p>
                            <p class="mt-1">{{ $role->description ?? 'No description' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Users</p>
                            <p class="mt-1">{{ $role->users->count() }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Permissions</p>
                            <p class="mt-1">{{ $role->permissions->count() }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Created At</p>
                            <p class="mt-1">{{ $role->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Updated At</p>
                            <p class="mt-1">{{ $role->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="mb-4 text-lg font-semibold">Permissions ({{ $role->permissions->count() }})</h3>

                    @php
                        $groupedPermissions = $role->permissions->groupBy(function ($permission) {
                            return explode('.', $permission->name)[0];
                        });
                    @endphp

                    @if ($groupedPermissions->count() > 0)
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($groupedPermissions as $group => $permissions)
                                <div class="p-4 border rounded">
                                    <h4 class="mb-2 font-semibold text-blue-600 capitalize">{{ $group }}</h4>
                                    <ul class="space-y-1 text-sm list-disc list-inside">
                                        @foreach ($permissions as $permission)
                                            <li class="text-gray-700">{{ $permission->display_name }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No permissions assigned to this role.</p>
                    @endif
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="mb-4 text-lg font-semibold">Users with this Role ({{ $role->users->count() }})</h3>

                    @if ($role->users->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Name</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Username</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($role->users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->username }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($user->is_active)
                                                <span
                                                    class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">Active</span>
                                            @else
                                                <span
                                                    class="inline-flex px-2 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                            @if (auth()->user()->hasPermission('user.view'))
                                                <a href="{{ route('users.show', $user) }}"
                                                    class="text-blue-600 hover:text-blue-900">View</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500">No users have been assigned this role yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-adminlte-layout>
