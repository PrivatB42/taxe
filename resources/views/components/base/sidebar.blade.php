<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="header-icon">
            <i class="fas fa-user-shield"></i>
        </div>
        <div>
            <h3 class="mb-0">
              <i class="fas fa-user-shield"></i>  {{ config('app.name_back') }}
            </h3>
        </div>
    </div>

    <ul class="sidebar-menu">
        @foreach (x_menu() as $menu)
        <li class="{{ $menu->hasSubMenu() ? 'has-submenu' : '' }} {{ $menu->active ? 'menu-active' : '' }} ">
            <a href="{{ $menu->hasSubMenu() ? '#' : $menu->route }}">
                <i class="{{ $menu->icon }}"></i>
                <span class="menu-text">{{ $menu->name }}</span>
                @if($menu->badge !== null)
                <span class="badge bg-danger ms-2">{{ $menu->badge }}</span>
                @endif
            </a>
            @if($menu->hasSubMenu())
            <ul class="submenu">
                @foreach($menu->subMenu as $subMenu)
                <li  class="{{ $subMenu->active ? 'active' : '' }}">
                    <a href="{{ $subMenu->route }}">
                        <i class="{{ $subMenu->icon }}"></i>
                        <span class="menu-text">{{ $subMenu->name }}</span>
                        @if($subMenu->badge !== null)
                        <span class="badge bg-danger ms-2">{{ $subMenu->badge }}</span>
                        @endif
                    </a>
                </li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
    </ul>
</div>