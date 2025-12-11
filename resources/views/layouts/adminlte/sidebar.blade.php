<aside class="shadow app-sidebar bg-body-secondary" data-bs-theme="dark">
    {{-- Sidebar Brand --}}
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="block w-auto text-white fill-current h-9" />
        </a>
    </div>

    {{-- Sidebar Wrapper --}}
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                aria-label="Main navigation" data-accordion="false">

                {{-- Loop menu hasil project kamu --}}
                @foreach ($menus as $menu)
                    @if ($menu->children && $menu->children->isNotEmpty())
                        @php
                            // Parent dianggap aktif kalau dia aktif atau salah satu child aktif
                            $isParentActive = $menu->active || $menu->children->contains(fn($c) => $c->active);
                        @endphp

                        <li class="nav-item {{ $isParentActive ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ $isParentActive ? 'active' : '' }}">
                                <i class="nav-icon {{ $menu->icon }}"></i>
                                <p>
                                    {{ $menu->display_name }}
                                    <i class="nav-arrow fas fa-chevron-right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">
                                @foreach ($menu->children as $child)
                                    @php
                                        $childUrl = '#';
                                        if ($child->route && Route::has($child->route)) {
                                            $childUrl = route($child->route);
                                        } elseif ($child->url) {
                                            $childUrl = $child->url;
                                        }
                                    @endphp
                                    <li class="pl-6 nav-item">
                                        <a href="{{ $childUrl }}"
                                            class="nav-link {{ $child->active ? 'active' : '' }}">
                                            <i class="nav-icon bi bi-dot"></i>
                                            <p>{{ $child->display_name }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        {{-- single menu --}}
                        @php
                            $menuUrl = '#';
                            if ($menu->route && Route::has($menu->route)) {
                                $menuUrl = route($menu->route);
                            } elseif ($menu->url) {
                                $menuUrl = $menu->url;
                            }
                        @endphp
                        <li class="nav-item">
                            <a href="{{ $menuUrl }}" class="nav-link {{ $menu->active ? 'active' : '' }}">
                                <i class="nav-icon {{ $menu->icon }}"></i>
                                <p>{{ $menu->display_name }}</p>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>
    </div>
</aside>
