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
                    @if (isset($menu['children']) && count($menu['children']))
                        {{-- treeview --}}
                        <li class="nav-item {{ $menu['active'] ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ $menu['active'] ? 'active' : '' }}">
                                <i class="nav-icon {{ $menu['icon'] }}"></i>
                                <p>
                                    {{ $menu['title'] }}
                                    <i class="nav-arrow fas fa-chevron-right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">
                                @foreach ($menu['children'] as $child)
                                    <li class="nav-item">
                                        <a href="{{ $child['url'] }}"
                                            class="nav-link {{ $child['active'] ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-circle"></i>
                                            <p>{{ $child['title'] }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        {{-- single menu --}}
                        <li class="nav-item">
                            <a href="{{ $menu['url'] }}" class="nav-link {{ $menu['active'] ? 'active' : '' }}">
                                <i class="nav-icon {{ $menu['icon'] }}"></i>
                                <p>{{ $menu['title'] }}</p>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>
    </div>
</aside>
