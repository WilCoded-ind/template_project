<x-adminlte-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Details') }}
            </h2>
            <a href="{{ route('users.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold mb-2">Basic Information</h3>
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
                                    @if ($user->is_active)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Created At</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 mt-6">
                        <h3 class="text-lg font-semibold mb-2">Assigned Roles</h3>
                        <div class="flex flex-wrap gap-2">
                            @forelse($user->roles as $role)
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $role->display_name }}
                                </span>
                            @empty
                                <p class="text-sm text-gray-500">No roles assigned</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="mb-4 mt-6">
                        <h3 class="text-lg font-semibold mb-2">Permissions</h3>
                        <div class="grid grid-cols-3 gap-2">
                            @php
                                $permissions = $user->getPermissions()->groupBy(function ($permission) {
                                    return explode('.', $permission->name)[0];
                                });
                            @endphp

                            @forelse($permissions as $module => $perms)
                                <div class="border rounded p-3">
                                    <h4 class="font-semibold text-sm text-gray-700 mb-2 capitalize">{{ $module }}
                                    </h4>
                                    <ul class="list-disc list-inside text-sm text-gray-600">
                                        @foreach ($perms as $perm)
                                            <li>{{ $perm->display_name }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 col-span-3">No permissions assigned</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        @if (auth()->user()->hasPermission('user.edit'))
                            <a href="{{ route('users.edit', $user) }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Edit User
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-adminlte-layout>
