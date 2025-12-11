{{-- Menu Action Buttons Component --}}
<div class="flex justify-start gap-2">
    {{-- View Button --}}
    <a href="{{ route('menus.show', $menu) }}" class="text-blue-600 hover:text-blue-900">
        Lihat
    </a>

    {{-- Edit Button --}}
    @if (auth()->user()->hasPermission('menu.edit'))
        <a href="{{ route('menus.edit', $menu) }}" class="text-indigo-600 hover:text-indigo-900">
            Edit
        </a>
    @endif

    {{-- Delete Button --}}
    @if (auth()->user()->hasPermission('menu.delete'))
        @if ($menu->parent_id === null && $menu->children()->count() > 0)
            <button disabled title="Tidak bisa hapus, ada sub-menu" class="text-gray-400 cursor-not-allowed">
                Hapus
            </button>
        @else
            <button onclick="deleteMenu({{ $menu->id }})" class="text-red-600 hover:text-red-900">
                Hapus
            </button>
        @endif
    @endif
</div>
