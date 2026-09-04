<aside
    class="app-sidebar"
    id="appSidebar">


    {{-- BRAND --}}

    <div class="sidebar-brand">

        <div class="brand-icon">
            <i class="bi bi-boxes"></i>
        </div>

        <div class="brand-text">

            <div class="brand-title">
                Production Management
            </div>

            <div class="brand-subtitle">
                Garment ERP
            </div>

        </div>

    </div>


    {{-- USER --}}

    @auth

        @php
            $user = auth()->user();

            $initials = collect(
                preg_split('/\s+/', trim($user->name))
            )
            ->filter()
            ->take(2)
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->implode('');
        @endphp

        <div class="sidebar-user">

            <div class="sidebar-avatar">
                {{ $initials ?: 'U' }}
            </div>

            <div class="sidebar-user-info">

                <div class="sidebar-user-name">
                    {{ $user->name }}
                </div>

                <div class="sidebar-user-role">
                    {{ $user->role }}
                </div>

            </div>

        </div>

    @endauth


    {{-- MAIN MENU --}}

    <div class="sidebar-section">

        <div class="sidebar-section-title">
            Main
        </div>

        <ul class="sidebar-menu">

            <li>

                <a
                    href="{{ auth()->user()->isAdmin()
                        ? route('admin.dashboard')
                        : route('user.dashboard') }}"
                    class="active">

                    <i class="bi bi-grid-1x2"></i>

                    <span>
                        Dashboard
                    </span>

                </a>

            </li>

        </ul>

    </div>


    {{-- DESIGN --}}

    <div class="sidebar-section">

        <div class="sidebar-section-title">
            Design
        </div>

        <ul class="sidebar-menu">

            <li class="nav-item">
                <a href="{{ route('design-specifications.index') }}"
                class="nav-link {{ request()->routeIs('design-specifications.*') ? 'active' : '' }}">

                    <i class="bi bi-palette2 nav-icon"></i>

                    <span>
                        Design Specification Master
                    </span>

                </a>
            </li>

        </ul>

    </div>


    {{-- PRODUCTION --}}

    <div class="sidebar-section">

        <div class="sidebar-section-title">
            Production
        </div>

        <ul class="sidebar-menu">

            <li>

                <a
                    href="{{ route('all-garments.index') }}"
                    class="{{ request()->routeIs('all-garments.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-grid-3x3-gap"></i>

                    <span>
                        All Garments
                    </span>

                </a>

            </li>

            <li>
                <a
                    href="{{ route('ai-description.index') }}"
                    class="{{ request()->routeIs('ai-description.*') ? 'active' : '' }}"
                >
                    <i class="bi bi-stars"></i>

                    <span>
                        Make AI Description
                    </span>
                </a>
            </li>

            <li>
                <a
                    href="{{ route('admin.publish-products.index') }}"
                    class="{{ request()->routeIs('admin.publish-products.*') ? 'active' : '' }}"
                >
                    <i class="bi bi-send-check"></i>

                    <span>
                        Publish Products
                    </span>
                </a>
            </li>

            <li>
                <a
                    href="{{ route('admin.published-products.index') }}"
                    class="{{ request()->routeIs('admin.published-products.*') ? 'active' : '' }}"
                >
                    <i class="bi bi-bag-check"></i>

                    <span>
                        Published Products
                    </span>
                </a>
            </li>

            <li>
                <a
                    href="{{ route('admin.website-orders.index') }}"
                    class="{{ request()->routeIs('admin.website-orders.*') ? 'active' : '' }}"
                >
                    <i class="bi bi-cart-check"></i>

                    <span>
                        Website Orders
                    </span>
                </a>
            </li>


            <li>
                <a href="{{ route('inventory.ready-to-sell-stock') }}">
                    <i class="bi bi-box-seam"></i>

                    <span>
                        Ready to Sell Stock
                    </span>
                </a>
            </li>

            <li>
                <a href="{{ route('inventory.ready-to-sell-stock.view-stock') }}">

                    <i class="bi bi-boxes"></i>

                    <span>
                        View Stock
                    </span>

                </a>
            </li>

            <li>
                <a href="{{ route('inventory.pattern-test-fit-stock') }}">

                    <i class="bi bi-grid-3x3-gap"></i>

                    <span>
                        Pattern & Test Fit Stock
                    </span>

                </a>
            </li>

            <li>
                <a href="{{ route('inventory.pattern-test-fit-stock.view') }}">

                    <i class="bi bi-boxes"></i>

                    <span>
                        View Pattern/Test Fit Stock
                    </span>

                </a>
            </li>

             <li>
                <a href="{{ route('inventory.fabric-yarn-buying') }}">

                    <i class="bi bi-boxes"></i>

                    <span>
                        Fabric-Yarn Buying Application
                    </span>

                </a>
            </li>


            <li>

                <a href="javascript:void(0)">

                    <i class="bi bi-upc-scan"></i>

                    <span>
                        Print Barcode
                    </span>

                </a>

            </li>

        </ul>

    </div>


    {{-- ADMIN --}}

    @if(auth()->user()->isAdmin())

        <div class="sidebar-section">

            <div class="sidebar-section-title">
                Administration
            </div>

            <ul class="sidebar-menu">

                <li>

                    <a href="javascript:void(0)">

                        <i class="bi bi-people"></i>

                        <span>
                            User Management
                        </span>

                    </a>

                </li>

                <li>
                    <a href="{{ route('admin.suppliers.index') }}" class="{{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i>
                        <span>
                            Suppliers
                        </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i>
                        <span>
                            Categories
                        </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.ai-photo-enhancers.index') }}" class="{{ request()->routeIs('admin.ai-photo-enhancers.*') ? 'active' : '' }}">
                        <i class="bi bi-camera"></i>
                        <span>
                            AI Photo Enhancers
                        </span>
                    </a>
                </li>

            </ul>
    </div>

    {{-- AI PHOTO ENHANCING --}}
    <div class="sidebar-section">
        <div class="sidebar-section-title">AI Photo Enhancing</div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('admin.ai-photo-enhancing.pending') }}"
                class="{{ request()->routeIs('admin.ai-photo-enhancing.pending') ? 'active' : '' }}">
                    <i class="bi bi-hourglass-split"></i>
                    <span>Pending Products</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.ai-photo-enhancing.receiving') }}"
                class="{{ request()->routeIs('admin.ai-photo-enhancing.receiving*') ? 'active' : '' }}">
                    <i class="bi bi-box-arrow-in-down"></i>
                    <span>Receiving Products</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">System</div>
        <ul class="sidebar-menu">
            <li>

                    <a href="{{ route('admin.module-access') }}">

                        <i class="bi bi-shield-lock"></i>

                        <span>
                            Module Access
                        </span>

                    </a>

                </li>


                <li>

                    <a href="javascript:void(0)">

                        <i class="bi bi-building"></i>

                        <span>
                            Company Settings
                        </span>

                    </a>

                </li>

            </ul>

        </div>

    @endif


    {{-- SYSTEM --}}

    <div class="sidebar-section">

        <div class="sidebar-section-title">
            System
        </div>

        <ul class="sidebar-menu">

            <li>

                <a href="javascript:void(0)">

                    <i class="bi bi-gear"></i>

                    <span>
                        Settings
                    </span>

                </a>

            </li>


            <li>

    <form
        method="POST"
        action="{{ route('logout') }}"
        class="logout-form m-0">

        @csrf

        <button
            type="submit"
            class="w-100 border-0 text-start"
            style="
                display:flex;
                align-items:center;
                gap:12px;
                min-height:44px;
                padding:9px 11px;
                border-radius:9px;
                background:transparent;
                color:#cbd5e1;
                font-size:13px;
                font-weight:500;
            ">

            <i
                class="bi bi-box-arrow-right"
                style="
                    width:21px;
                    text-align:center;
                    font-size:17px;
                ">
            </i>

            <span>
                Logout
            </span>

        </button>

    </form>

</li>

        </ul>

    </div>


    <div style="height:25px;"></div>

</aside>