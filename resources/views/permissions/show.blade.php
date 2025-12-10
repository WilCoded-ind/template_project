<x-adminlte-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Permission Details') }}
            </h2>
            <div class="flex gap-2">
                @if (auth()->user()->hasPermission('permission.edit'))
                    <a href="{{ route('permissions.edit', $permission) }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit Permission
                    </a>
                @endif
                <a href="{{ route('permissions.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Permission Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Name</p>
                            <p class="mt-1">
                                <span
                                    class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ $permission->name }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Display Name</p>
                            <p class="mt-1">{{ $permission->display_name }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-500">Description</p>
                            <p class="mt-1">{{ $permission->description ?? 'No description' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Created At</p>
                            <p class="mt-1">{{ $permission->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Updated At</p>
                            <p class="mt-1">{{ $permission->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Roles with this Permission
                        ({{ $permission->roles->count() }})</h3>

                    @if ($permission->roles->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach ($permission->roles as $role)
                                <div class="border rounded p-4">
                                    <h4 class="font-semibold text-blue-600">{{ $role->display_name }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ $role->description }}</p>
                                    <p class="text-xs text-gray-500 mt-2">{{ $role->users->count() }} users</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">This permission is not assigned to any role yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-adminlte-layout>
