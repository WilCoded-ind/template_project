<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
<div class="bg-white shadow">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Left Side: Logo & Navigation -->
            <div class="flex items-center min-w-0">
                <!-- Logo -->
                <div class="flex items-center shrink-0 mr-8">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block w-auto text-gray-800 fill-current h-9" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex items-center ml-12 gap-4">
                    @foreach($menus as $menu)
                        @if($menu->children->count() > 0)
                            <!-- Dropdown Menu -->
                            <div class="relative">
                                <x-dropdown align="left" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-4 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none space-x-2">
                                            @if($menu->icon)
                                                <i class="{{ $menu->icon }} mr-2"></i>
                                            @endif
                                            <span>{{ __($menu->display_name) }}</span>
                                            <div class="ml-2">
                                                <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        @foreach($menu->children as $child)
                                            @if($child->route)
                                                <x-dropdown-link :href="route($child->route)">
                                                    @if($child->icon)
                                                        <i class="{{ $child->icon }} mr-2"></i>
                                                    @endif
                                                    {{ __($child->display_name) }}
                                                </x-dropdown-link>
                                            @elseif($child->url)
                                                <x-dropdown-link :href="$child->url">
                                                    @if($child->icon)
                                                        <i class="{{ $child->icon }} mr-2"></i>
                                                    @endif
                                                    {{ __($child->display_name) }}
                                                </x-dropdown-link>
                                            @endif
                                        @endforeach
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        @else
                            <!-- Single Menu Item -->
                            @if($menu->route)
                                <x-nav-link :href="route($menu->route)" :active="request()->routeIs($menu->route)">
                                    @if($menu->icon)
                                        <i class="{{ $menu->icon }} mr-2"></i>
                                    @endif
                                    {{ __($menu->display_name) }}
                                </x-nav-link>
                            @elseif($menu->url)
                                <x-nav-link :href="$menu->url" :active="request()->is(ltrim($menu->url, '/'))">
                                    @if($menu->icon)
                                        <i class="{{ $menu->icon }} mr-2"></i>
                                    @endif
                                    {{ __($menu->display_name) }}
                                </x-nav-link>
                            @endif
                        @endif
                    @endforeach
                </div>
            </div>
