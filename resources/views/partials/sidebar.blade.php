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

                <a href="javascript:void(0)">

                    <i class="bi bi-grid-3x3-gap"></i>

                    <span>
                        All Garments
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