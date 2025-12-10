<x-adminlte-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Menu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('menus.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Name (slug) <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                <p class="mt-1 text-sm text-gray-500">Use lowercase letters, numbers, and dashes (e.g.,
                                    user-management)</p>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="display_name" class="block text-sm font-medium text-gray-700">Display Name
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="display_name" id="display_name"
                                    value="{{ old('display_name') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                @error('display_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="icon" class="block text-sm font-medium text-gray-700">Icon
                                    (FontAwesome)</label>
                                <input type="text" name="icon" id="icon" value="{{ old('icon') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="fas fa-home">
                                <p class="mt-1 text-sm text-gray-500">Example: fas fa-home, fas fa-users</p>
                                @error('icon')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent
                                    Menu</label>
                                <select name="parent_id" id="parent_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">None (Parent Menu)</option>
                                    @foreach ($parentMenus as $parentMenu)
                                        <option value="{{ $parentMenu->id }}"
                                            {{ old('parent_id') == $parentMenu->id ? 'selected' : '' }}>
                                            {{ $parentMenu->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="route" class="block text-sm font-medium text-gray-700">Route Name</label>
                                <input type="text" name="route" id="route" value="{{ old('route') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="users.index">
                                <p class="mt-1 text-sm text-gray-500">Laravel route name (e.g., users.index)</p>
                                @error('route')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="url" class="block text-sm font-medium text-gray-700">URL
                                    (Alternative)</label>
                                <input type="text" name="url" id="url" value="{{ old('url') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="/custom-url">
                                <p class="mt-1 text-sm text-gray-500">Use this if not using route name</p>
                                @error('url')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="permission_id"
                                    class="block text-sm font-medium text-gray-700">Permission</label>
                                <select name="permission_id" id="permission_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">None (Accessible by all)</option>
                                    @foreach ($permissions as $permission)
                                        <option value="{{ $permission->id }}"
                                            {{ old('permission_id') == $permission->id ? 'selected' : '' }}>
                                            {{ $permission->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('permission_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="order" class="block text-sm font-medium text-gray-700">Order <span
                                        class="text-red-500">*</span></label>
                                <input type="number" name="order" id="order" value="{{ old('order', 0) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required min="0">
                                <p class="mt-1 text-sm text-gray-500">Lower number appears first</p>
                                @error('order')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4 mt-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('menus.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Cancel
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Menu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-adminlte-layout>
