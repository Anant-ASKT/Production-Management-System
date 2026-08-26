<header class="app-topbar">
    <div class="topbar-left">
        <button class="topbar-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-title">
            @yield('page-title', 'AI Enhancer Portal')
        </div>
    </div>

    <div class="topbar-right">
        <!-- Add topbar actions if needed -->
        <form method="POST" action="{{ route('ai-enhancer.logout') }}" class="m-0 d-none d-md-block">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</header>