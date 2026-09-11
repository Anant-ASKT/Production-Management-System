<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sampling Portal') | Fashion ERP</title>

    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #1e3a5f;
            --primary-dark: #12243b;
            --primary-light: #e8f0fe;
            --accent: #c98a4b;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --body-bg: #f8fafc;
            --card-border: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--body-bg);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .sampling-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sampling-sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: #cbd5e1;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            transition: all 0.25s ease;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent) 0%, #a86c32 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.25rem;
            box-shadow: 0 4px 12px rgba(201, 138, 75, 0.35);
        }

        .sidebar-menu {
            list-style: none;
            padding: 16px 12px;
            margin: 0;
            flex-grow: 1;
        }

        .menu-header {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            padding: 12px 14px 6px;
            font-weight: 600;
        }

        .menu-item {
            margin-bottom: 4px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.92rem;
            transition: all 0.15s ease;
        }

        .menu-link:hover {
            color: #ffffff;
            background-color: var(--sidebar-hover);
        }

        .menu-link.active {
            color: #ffffff;
            background: linear-gradient(90deg, #1e3a5f 0%, #2b5288 100%);
            border-left: 3px solid var(--accent);
            font-weight: 600;
        }

        .menu-link i {
            font-size: 1.15rem;
        }

        .menu-submenu {
            list-style: none;
            padding-left: 20px;
            margin-top: 4px;
            margin-bottom: 6px;
        }

        .submenu-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 12px;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.86rem;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .submenu-link:hover {
            color: #ffffff;
            background-color: var(--sidebar-hover);
        }

        .submenu-link.active {
            color: #ffffff;
            background-color: #1e3a5f;
            border-left: 3px solid var(--accent);
            font-weight: 600;
        }

        /* Main Content Container */
        .sampling-main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* Top Header */
        .sampling-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 16px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 100;
        }

        .content-body {
            padding: 24px 30px;
            flex-grow: 1;
        }

        .card {
            border-radius: 12px;
            border: 1px solid var(--card-border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        /* Highlight Ribbon for Production Notes */
        .production-notes-alert {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 1px solid #fde68a;
            border-left: 5px solid #f59e0b;
            border-radius: 10px;
            padding: 16px 20px;
        }

        /* Voice Note Audio Component */
        .voice-note-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sampling-wrapper">
        {{-- Sidebar --}}
        <aside class="sampling-sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <i class="bi bi-palette"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-white">Fashion Studio</h6>
                    <small class="text-white-50" style="font-size: 0.75rem;">Sampling Module</small>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="menu-header">WORKSPACE</li>
                <li class="menu-item">
                    <a href="{{ route('sampling.dashboard') }}" class="menu-link {{ request()->routeIs('sampling.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('sampling.projects.index') }}" class="menu-link {{ request()->routeIs('sampling.projects.*') ? 'active' : '' }}">
                        <i class="bi bi-folder2-open"></i>
                        <span>Projects & Batches</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('sampling.samples.index') }}" class="menu-link {{ request()->routeIs('sampling.samples.index') || request()->routeIs('sampling.samples.show') || request()->routeIs('sampling.samples.create') ? 'active' : '' }}">
                        <i class="bi bi-layers-half"></i>
                        <span>All Samples</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('sampling.samples.index', ['filter' => 'frozen']) }}" class="menu-link {{ request('filter') === 'frozen' ? 'active' : '' }}">
                        <i class="bi bi-snow"></i>
                        <span>Frozen Masters</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('sampling.users.index') }}" class="menu-link {{ request()->routeIs('sampling.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Team & Users</span>
                    </a>
                </li>

                <li class="menu-header">DOCUMENTATION</li>
                <li class="menu-item">
                    <a href="{{ route('sampling.storage.index') }}" class="menu-link {{ request()->routeIs('sampling.storage.*') ? 'active' : '' }}">
                        <i class="bi bi-archive"></i>
                        <span>Sample Storage</span>
                    </a>
                </li>
                <li class="menu-header">MASTERS</li>
                <li class="menu-item">
                    <a class="menu-link {{ request()->routeIs('sampling.masters.*') ? 'active' : '' }}" 
                       data-bs-toggle="collapse" 
                       href="#mastersSubmenu" 
                       role="button" 
                       aria-expanded="{{ request()->routeIs('sampling.masters.*') ? 'true' : 'false' }}">
                        <i class="bi bi-gear-wide-connected"></i>
                        <span>Studio Masters</span>
                        <i class="bi bi-chevron-down ms-auto" style="font-size: 0.75rem;"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('sampling.masters.*') ? 'show' : '' }}" id="mastersSubmenu">
                        <ul class="menu-submenu">
                            <li>
                                <a href="{{ route('sampling.masters.departments.index') }}" class="submenu-link {{ request()->routeIs('sampling.masters.departments.*') || request()->routeIs('sampling.masters.divisions.*') ? 'active' : '' }}">
                                    <i class="bi bi-diagram-2"></i>
                                    <span>Departments</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sampling.masters.skill-levels.index') }}" class="submenu-link {{ request()->routeIs('sampling.masters.skill-levels.*') ? 'active' : '' }}">
                                    <i class="bi bi-award"></i>
                                    <span>Skill Levels</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sampling.masters.operations.index') }}" class="submenu-link {{ request()->routeIs('sampling.masters.operations.*') ? 'active' : '' }}">
                                    <i class="bi bi-gear-wide"></i>
                                    <span>Operations</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sampling.masters.materials.index') }}" class="submenu-link {{ request()->routeIs('sampling.masters.materials.*') ? 'active' : '' }}">
                                    <i class="bi bi-box-seam"></i>
                                    <span>Materials</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sampling.masters.measurement-points.index') }}" class="submenu-link {{ request()->routeIs('sampling.masters.measurement-points.*') ? 'active' : '' }}">
                                    <i class="bi bi-rulers"></i>
                                    <span>Measurements</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sampling.masters.locations.index') }}" class="submenu-link {{ request()->routeIs('sampling.masters.locations.*') ? 'active' : '' }}">
                                    <i class="bi bi-archive"></i>
                                    <span>Storage Locations</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sampling.masters.uoms.index') }}" class="submenu-link {{ request()->routeIs('sampling.masters.uoms.*') ? 'active' : '' }}">
                                    <i class="bi bi-calculator"></i>
                                    <span>Units (UOM)</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sampling.masters.designers.index') }}" class="submenu-link {{ request()->routeIs('sampling.masters.designers.*') ? 'active' : '' }}">
                                    <i class="bi bi-palette"></i>
                                    <span>Designers</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sampling.masters.collections.index') }}" class="submenu-link {{ request()->routeIs('sampling.masters.collections.*') ? 'active' : '' }}">
                                    <i class="bi bi-collection"></i>
                                    <span>Collections</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sampling.masters.specs.index') }}" class="submenu-link {{ request()->routeIs('sampling.masters.specs.*') ? 'active' : '' }}">
                                    <i class="bi bi-card-checklist"></i>
                                    <span>Tech Specs</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sampling.masters.reference-types.index') }}" class="submenu-link {{ request()->routeIs('sampling.masters.reference-types.*') ? 'active' : '' }}">
                                    <i class="bi bi-images"></i>
                                    <span>Sketch & Swatch Types</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>

            {{-- Company Footer --}}
            <div class="p-3 border-top border-secondary border-opacity-25 mt-auto">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 34px; height: 34px; font-size: 0.85rem;">
                        {{ substr(auth()->guard('sampling')->user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-white text-truncate fw-semibold small">{{ auth()->guard('sampling')->user()->name ?? 'Artisan' }}</div>
                        <div class="text-white-50 text-truncate" style="font-size: 0.72rem;">{{ auth()->guard('sampling')->user()->company->name ?? 'Studio' }}</div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Area --}}
        <div class="sampling-main">
            {{-- Top Navbar --}}
            <header class="sampling-header">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="m-0 fw-bold text-dark">@yield('page-title', 'Dashboard')</h5>
                    @if(auth()->guard('sampling')->check())
                        <span class="badge bg-light text-dark border px-2 py-1 small">
                            <i class="bi bi-building me-1 text-primary"></i> {{ auth()->guard('sampling')->user()->company->name }}
                        </span>
                    @endif
                </div>

                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 text-capitalize">
                        {{ str_replace('_', ' ', auth()->guard('sampling')->user()->role ?? 'Staff') }}
                    </span>

                    <form action="{{ route('sampling.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </header>

            {{-- Main Body --}}
            <main class="content-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0 small">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- Bootstrap JS Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
