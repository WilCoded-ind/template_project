<aside class="shadow app-sidebar bg-body-secondary" data-bs-theme="dark">
    {{-- Sidebar Brand --}}
    <div class="sidebar-brand">
        <a href="/" class="brand-link">
            <img src="{{ asset('adminlte/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                class="shadow opacity-75 brand-image">
            <span class="brand-text fw-light">AdminLTE 4</span>
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
                        {{-- treeview --}}
                        <li class="nav-item {{ $menu->active ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ $menu->active ? 'active' : '' }}">
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
                                    <li class="nav-item">
                                        <a href="{{ $childUrl }}"
                                            class="nav-link {{ $child->active ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-circle"></i>
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
