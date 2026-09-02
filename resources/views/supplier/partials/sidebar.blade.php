<aside class="app-sidebar" id="appSidebar">

    {{-- BRAND --}}
    <!-- <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-boxes"></i>
        </div>
        <div class="brand-text">
            <div class="brand-title">Production Management</div>
            <div class="brand-subtitle">Garment ERP</div>
        </div>
    </div> -->

    {{-- USER --}}
    @php
        $user = auth()->guard('supplier')->user();
        $initials = collect(preg_split('/\s+/', trim($user->name ?? 'Supplier')))
            ->filter()
            ->take(2)
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->implode('');
    @endphp

    <div class="sidebar-user">
        <div class="sidebar-avatar">
            {{ $initials ?: 'S' }}
        </div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name">
                {{ $user->name ?? 'User' }}
            </div>
            <div class="sidebar-user-role">
                {{ $user->supplier->name ?? 'Supplier' }}
            </div>
        </div>
    </div>

    {{-- MAIN MENU --}}
    <div class="sidebar-section">
        <div class="sidebar-section-title">Main</div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('supplier.dashboard') }}" class="{{ request()->routeIs('supplier.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- CATALOG --}}
    <div class="sidebar-section">
        <div class="sidebar-section-title">Catalog</div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('supplier.products.index') }}" class="{{ request()->routeIs('supplier.products.*') ? 'active' : '' }}">
                    <i class="bi bi-boxes"></i>
                    <span>My Products</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- SYSTEM --}}
    <div class="sidebar-section">
        <div class="sidebar-section-title">System</div>
        <ul class="sidebar-menu">
            <li>
                <form method="POST" action="{{ route('supplier.logout') }}" class="logout-form m-0">
                    @csrf
                    <button type="submit" class="w-100 border-0 text-start" style="display:flex; align-items:center; gap:12px; min-height:44px; padding:9px 11px; border-radius:9px; background:transparent; color:#cbd5e1; font-size:13px; font-weight:500;">
                        <i class="bi bi-box-arrow-right" style="width:21px; text-align:center; font-size:17px;"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <div style="height:25px;"></div>
</aside>