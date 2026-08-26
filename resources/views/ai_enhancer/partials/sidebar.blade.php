<aside class="app-sidebar" id="appSidebar">

    {{-- BRAND --}}
    <!-- <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-camera"></i>
        </div>
        <div class="brand-text">
            <div class="brand-title">AI Enhancer</div>
            <div class="brand-subtitle">Portal</div>
        </div>
    </div> -->

    {{-- USER --}}
    @auth('ai_enhancer')
        @php
            $user = auth()->guard('ai_enhancer')->user();
            $initials = collect(preg_split('/\s+/', trim($user->first_name)))
                ->filter()->take(2)->map(fn($word) => strtoupper(substr($word, 0, 1)))->implode('');
        @endphp
        <div class="sidebar-user">
            <div class="sidebar-avatar">{{ $initials ?: 'E' }}</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ $user->first_name }} {{ $user->last_name }}</div>
                <div class="sidebar-user-role">Enhancer</div>
            </div>
        </div>
    @endauth

    {{-- MAIN MENU --}}
    <div class="sidebar-section">
        <div class="sidebar-section-title">Main</div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('ai-enhancer.dashboard') }}" class="{{ request()->routeIs('ai-enhancer.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('ai-enhancer.assigned-products.index') }}" class="{{ request()->routeIs('ai-enhancer.assigned-products.*') ? 'active' : '' }}">
                    <i class="bi bi-list-task"></i>
                    <span>Assigned Products</span>
                </a>
            </li>
            <li>
                <a href="{{ route('ai-enhancer.upload-history.index') }}" class="{{ request()->routeIs('ai-enhancer.upload-history.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Upload History</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- SYSTEM --}}
    <div class="sidebar-section">
        <div class="sidebar-section-title">System</div>
        <ul class="sidebar-menu">
            <li>
                <form method="POST" action="{{ route('ai-enhancer.logout') }}" class="logout-form m-0">
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