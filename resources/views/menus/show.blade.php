<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Menu Details') }}
            </h2>
            <div class="flex gap-2">
                @if(auth()->user()->hasPermission('menu.edit'))
                <a href="{{ route('menus.edit', $menu) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Menu
                </a>
                @endif
                <a href="{{ route('menus.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Menu Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Name</p>
                            <p class="mt-1">{{ $menu->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Display Name</p>
                            <p class="mt-1">{{ $menu->display_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Icon</p>
                            <p class="mt-1">
                                @if($menu->icon)
                                    <i class="{{ $menu->icon }} mr-2"></i>{{ $menu->icon }}
                                @else
                                    <span class="text-gray-400">No icon</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Parent Menu</p>
                            <p class="mt-1">
                                @if($menu->parent)
                                    <a href="{{ route('menus.show', $menu->parent) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $menu->parent->display_name }}
                                    </a>
                                @else
                                    <span class="text-gray-400">None (Parent Menu)</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Route Name</p>
                            <p class="mt-1">{{ $menu->route ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">URL</p>
                            <p class="mt-1">{{ $menu->url ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Permission</p>
                            <p class="mt-1">
                                @if($menu->permission)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        {{ $menu->permission->display_name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">None (Accessible by all)</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Order</p>
                            <p class="mt-1">{{ $menu->order }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <p class="mt-1">
                                @if($menu->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Created At</p>
                            <p class="mt-1">{{ $menu->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Updated At</p>
                            <p class="mt-1">{{ $menu->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($menu->children->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Sub Menus ({{ $menu->children->count() }})</h3>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Display Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Icon</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route/URL</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($menu->children as $child)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $child->display_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($child->icon)
                                        <i class="{{ $child->icon }}"></i>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $child->route ?? $child->url ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $child->order }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($child->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('menus.show', $child) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    @if(auth()->user()->hasPermission('menu.edit'))
                                    <a href="{{ route('menus.edit', $child) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
