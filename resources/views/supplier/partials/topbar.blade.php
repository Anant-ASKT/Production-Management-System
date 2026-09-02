<header class="app-topbar">


    <div class="topbar-left">

        <button
            type="button"
            class="sidebar-toggle"
            id="sidebarToggle">

            <i class="bi bi-list"></i>

        </button>


        <div>

            <h1 class="topbar-page-title">

                @yield('page-title', 'Dashboard')

            </h1>

            <div class="topbar-breadcrumb">

                Production Management System

            </div>

        </div>

    </div>


    <div class="topbar-right">


        {{-- Notification --}}

        <button
            type="button"
            class="topbar-icon"
            title="Notifications">

            <i class="bi bi-bell"></i>

        </button>


        {{-- User --}}

        @auth

    @php

        $user = auth()->guard('supplier')->user();

        $initials = collect(
            preg_split('/\s+/', trim($user->name ?? 'Supplier'))
        )
        ->filter()
        ->take(2)
        ->map(
            fn($word) =>
                strtoupper(substr($word, 0, 1))
        )
        ->implode('');

    @endphp


    <div class="dropdown">

        <button
            type="button"
            class="topbar-user border-0 bg-transparent"
            data-bs-toggle="dropdown"
            aria-expanded="false">

            <div class="topbar-user-avatar">

                {{ $initials ?: 'U' }}

            </div>


            <div class="topbar-user-info">

                <div class="topbar-user-name">
                    {{ $user->name ?? 'User' }}
                </div>
                <div class="topbar-user-role">
                    {{ $user->supplier->name ?? 'Supplier' }}
                </div>
            </div>

            <i class="bi bi-chevron-down ms-1" style="font-size:11px;"></i>
        </button>

        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="min-width:210px;border-radius:12px;">
            <li>
                <div class="px-3 py-2">
                    <div style="font-size:13px; font-weight:700;">
                        {{ $user->name }}
                    </div>
                    <div class="text-muted" style="font-size:11px;">
                        {{ $user->email }}
                    </div>
                </div>
            </li>


            <li>
                <hr class="dropdown-divider">
            </li>


            <li>

                <a
                    class="dropdown-item"
                    href="javascript:void(0)">

                    <i class="bi bi-person me-2"></i>

                    My Profile

                </a>

            </li>


            <li>

                <a
                    class="dropdown-item"
                    href="javascript:void(0)">

                    <i class="bi bi-gear me-2"></i>

                    Settings

                </a>

            </li>


            <li>
                <hr class="dropdown-divider">
            </li>


            <li>

                <form
                    action="{{ route('supplier.logout') }}"
                    method="POST"
                    class="logout-form m-0"
                    id="topbarLogoutForm">

                    @csrf

                    <button
                        type="submit"
                        class="dropdown-item text-danger">

                        <i class="bi bi-box-arrow-right me-2"></i>

                        Logout

                    </button>

                </form>

            </li>

        </ul>

    </div>

@endauth

    </div>

</header>