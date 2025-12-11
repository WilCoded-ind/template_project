{{-- User Action Buttons Component --}}
<div class="flex justify-center gap-2">
    {{-- View Button --}}
    @if (auth()->user()->hasPermission('user.view'))
        <a href="{{ route('users.show', $user) }}"
            class="px-2 py-1 text-sm font-semibold text-white bg-gray-600 rounded hover:bg-gray-800" title="View">
            <i class="fa-solid fa-eye"></i>
        </a>
    @endif

    {{-- Edit Button --}}
    @if (auth()->user()->hasPermission('user.edit'))
        <a href="{{ route('users.edit', $user) }}"
            class="px-2 py-1 text-sm font-semibold text-white bg-yellow-500 rounded hover:bg-yellow-600" title="Edit">
            <i class="fa-solid fa-pen"></i>
        </a>
    @endif

    {{-- Delete Button --}}
    @if (auth()->user()->hasPermission('user.delete') && $user->id !== auth()->id())
        <button type="button" onclick="deleteUser({{ $user->id }})"
            class="px-2 py-1 text-sm font-semibold text-white bg-red-600 rounded hover:bg-red-700" title="Delete">
            <i class="fas fa-trash"></i>
        </button>
    @endif
</div>
