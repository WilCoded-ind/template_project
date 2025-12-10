<x-adminlte-layout>
    <div>
        <div class="flex items-center justify-between">
            <h1 class="my-1 text-3xl font-semibold text-gray-900">{{ __('User Details') }}</h1>
            <a href="{{ route('users.index') }}" class="px-4 py-2 font-bold text-white bg-gray-500 rounded hover:bg-gray-700">
                Back to List
            </a>
        </div>
    </div>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="mb-2 text-lg font-semibold">Basic Information</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Username</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->username }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <p class="mt-1 text-sm">
                                    @if($user->is_active)
                                        <span class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">Active</span>
                                    @else
                                        <span class="inline-flex px-2 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full">Inactive</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Created At</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 mb-4">
                        <h3 class="mb-2 text-lg font-semibold">Assigned Roles</h3>
                        <div class="flex flex-wrap gap-2">
                            @forelse($user->roles as $role)
                                <span class="inline-flex px-3 py-1 text-sm font-semibold leading-5 text-blue-800 bg-blue-100 rounded-full">
                                    {{ $role->display_name }}
                                </span>
                            @empty
                                <p class="text-sm text-gray-500">No roles assigned</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="mt-6 mb-4">
                        <h3 class="mb-2 text-lg font-semibold">Permissions</h3>
                        <div class="grid grid-cols-3 gap-2">
                            @php
                                $permissions = $user->getPermissions()->groupBy(function($permission) {
                                    return explode('.', $permission->name)[0];
                                });
                            @endphp

                            @forelse($permissions as $module => $perms)
                                <div class="p-3 border rounded">
                                    <h4 class="mb-2 text-sm font-semibold text-gray-700 capitalize">{{ $module }}</h4>
                                    <ul class="text-sm text-gray-600 list-disc list-inside">
                                        @foreach($perms as $perm)
                                            <li>{{ $perm->display_name }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @empty
                                <p class="col-span-3 text-sm text-gray-500">No permissions assigned</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        @if(auth()->user()->hasPermission('user.edit'))
                        <a href="{{ route('users.edit', $user) }}" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                            Edit User
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-adminlte-layout>
