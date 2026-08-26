<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>
        @yield('title', 'AI Photo Enhancer Portal') - PM System
    </title>

    {{-- Bootstrap --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    {{-- Select2 CSS --}}
    <link
        href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
        rel="stylesheet">

    <style>

        :root {

            --primary: #2b5288;

            --primary-dark: #1f3f6b;

            --primary-light: #eaf1f9;

            --sidebar: #0f172a;

            --sidebar-hover: #1e293b;

            --body-bg: #f5f7fb;

            --text-dark: #172033;

            --text-muted: #64748b;

            --border: #e2e8f0;

            --white: #ffffff;

            --danger: #dc3545;

        }


        * {
            box-sizing: border-box;
        }


        html,
        body {

            margin: 0;

            padding: 0;

            width: 100%;

            min-height: 100%;

            font-family:
                Inter,
                "Segoe UI",
                Arial,
                sans-serif;

            background: var(--body-bg);

            color: var(--text-dark);

        }


        body {
            overflow-x: hidden;
        }


        /* =========================================================
           APP WRAPPER
        ========================================================= */

        .app-wrapper {

            display: flex;

            min-height: 100vh;

            width: 100%;

        }


        /* =========================================================
           SIDEBAR
        ========================================================= */

        .app-sidebar {

            position: fixed;

            top: 0;

            left: 0;

            bottom: 0;

            width: 260px;

            background: var(--sidebar);

            color: #ffffff;

            z-index: 1040;

            transition:
                width .25s ease,
                transform .25s ease;

            overflow-y: auto;

            overflow-x: hidden;

            scrollbar-width: thin;

        }


        .sidebar-brand {

            height: 72px;

            display: flex;

            align-items: center;

            gap: 12px;

            padding: 0 20px;

            border-bottom:
                1px solid rgba(255,255,255,.08);

        }


        .brand-icon {

            width: 40px;

            height: 40px;

            flex: 0 0 40px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 11px;

            background: var(--primary);

            color: #ffffff;

            font-size: 20px;

        }


        .brand-text {

            min-width: 0;

        }


        .brand-title {

            margin: 0;

            color: #ffffff;

            font-size: 15px;

            font-weight: 700;

            white-space: nowrap;

        }


        .brand-subtitle {

            margin: 2px 0 0;

            color: #94a3b8;

            font-size: 10px;

            white-space: nowrap;

            text-transform: uppercase;

            letter-spacing: .7px;

        }


        /* Sidebar user */

        .sidebar-user {

            margin: 18px 14px;

            padding: 12px;

            border-radius: 12px;

            background:
                rgba(255,255,255,.05);

            display: flex;

            align-items: center;

            gap: 10px;

        }


        .sidebar-avatar {

            width: 38px;

            height: 38px;

            flex: 0 0 38px;

            border-radius: 50%;

            background: var(--primary);

            display: flex;

            align-items: center;

            justify-content: center;

            font-weight: 700;

            font-size: 14px;

        }


        .sidebar-user-info {

            min-width: 0;

        }


        .sidebar-user-name {

            color: #ffffff;

            font-size: 13px;

            font-weight: 600;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;

        }


        .sidebar-user-role {

            color: #94a3b8;

            font-size: 11px;

            text-transform: capitalize;

        }


        /* Sidebar menu */

        .sidebar-section {

            padding: 0 14px;

            margin-top: 20px;

        }


        .sidebar-section-title {

            padding: 0 10px;

            margin-bottom: 7px;

            color: #64748b;

            font-size: 10px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: 1px;

        }


        .sidebar-menu {

            list-style: none;

            padding: 0;

            margin: 0;

        }


        .sidebar-menu li {

            margin-bottom: 3px;

        }


        .sidebar-menu a {

            display: flex;

            align-items: center;

            gap: 12px;

            min-height: 44px;

            padding: 9px 11px;

            border-radius: 9px;

            color: #cbd5e1;

            text-decoration: none;

            font-size: 13px;

            font-weight: 500;

            transition: .2s;

        }


        .sidebar-menu a i {

            width: 21px;

            text-align: center;

            font-size: 17px;

            flex: 0 0 21px;

        }


        .sidebar-menu a:hover {

            background: var(--sidebar-hover);

            color: #ffffff;

        }


        .sidebar-menu a.active {

            background: var(--primary);

            color: #ffffff;

        }


        .sidebar-menu .menu-arrow {

            margin-left: auto;

            font-size: 12px;

        }


        /* =========================================================
           MAIN AREA
        ========================================================= */

        .app-main {

            width: calc(100% - 260px);

            margin-left: 260px;

            min-height: 100vh;

            transition:
                width .25s ease,
                margin-left .25s ease;

        }


        /* =========================================================
           TOPBAR
        ========================================================= */

        .app-topbar {

            position: sticky;

            top: 0;

            z-index: 1030;

            height: 72px;

            background: rgba(255,255,255,.96);

            backdrop-filter: blur(10px);

            border-bottom:
                1px solid var(--border);

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 25px;

        }


        .topbar-left {

            display: flex;

            align-items: center;

            gap: 15px;

            min-width: 0;

        }


        .sidebar-toggle {

            width: 40px;

            height: 40px;

            border: 0;

            border-radius: 9px;

            background: var(--primary-light);

            color: var(--primary);

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 20px;

        }


        .topbar-page-title {

            margin: 0;

            font-size: 18px;

            font-weight: 700;

            color: var(--text-dark);

        }


        .topbar-breadcrumb {

            margin: 2px 0 0;

            color: var(--text-muted);

            font-size: 11px;

        }


        .topbar-right {

            display: flex;

            align-items: center;

            gap: 10px;

        }


        .topbar-icon {

            width: 40px;

            height: 40px;

            border: 1px solid var(--border);

            background: #ffffff;

            color: var(--text-muted);

            border-radius: 9px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 17px;

        }


        .topbar-icon:hover {

            color: var(--primary);

            background: var(--primary-light);

        }


        .topbar-user {

            display: flex;

            align-items: center;

            gap: 9px;

            padding: 4px 8px 4px 4px;

            border-radius: 10px;

        }


        .topbar-user-avatar {

            width: 36px;

            height: 36px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            background: var(--primary);

            color: #ffffff;

            font-size: 13px;

            font-weight: 700;

        }


        .topbar-user-name {

            font-size: 12px;

            font-weight: 600;

            color: var(--text-dark);

        }


        .topbar-user-role {

            font-size: 10px;

            color: var(--text-muted);

            text-transform: capitalize;

        }


        /* =========================================================
           CONTENT
        ========================================================= */

        .app-content {

            padding: 25px;

        }


        /* =========================================================
           OVERLAY
        ========================================================= */

        .sidebar-overlay {

            display: none;

            position: fixed;

            inset: 0;

            background: rgba(15,23,42,.55);

            z-index: 1035;

        }


        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 991.98px) {

            .app-sidebar {

                transform: translateX(-100%);

                width: 260px;

            }


            .app-sidebar.show {

                transform: translateX(0);

            }


            .sidebar-overlay.show {

                display: block;

            }


            .app-main {

                width: 100%;

                margin-left: 0;

            }


            .app-topbar {

                padding: 0 15px;

            }


            .app-content {

                padding: 18px;

            }


            .topbar-user-info {

                display: none;

            }

        }


        @media (max-width: 575.98px) {

            .app-topbar {

                height: 64px;

            }


            .topbar-page-title {

                font-size: 16px;

            }


            .topbar-breadcrumb {

                display: none;

            }


            .topbar-icon {

                width: 36px;

                height: 36px;

            }


            .topbar-user-avatar {

                width: 34px;

                height: 34px;

            }


            .app-content {

                padding: 14px;

            }

        }


        /* =========================================================
           DASHBOARD
        ========================================================= */

        .welcome-card {

            position: relative;

            overflow: hidden;

            border: 0;

            border-radius: 16px;

            padding: 25px;

            background:
                linear-gradient(
                    135deg,
                    #2b5288,
                    #193b68
                );

            color: #ffffff;

            box-shadow:
                0 10px 30px
                rgba(43,82,136,.18);

        }


        .welcome-card::after {

            content: "";

            position: absolute;

            width: 220px;

            height: 220px;

            border-radius: 50%;

            right: -80px;

            top: -100px;

            background:
                rgba(255,255,255,.08);

        }


        .welcome-card h2 {

            position: relative;

            z-index: 1;

            margin: 0 0 7px;

            font-size: 23px;

            font-weight: 700;

        }


        .welcome-card p {

            position: relative;

            z-index: 1;

            margin: 0;

            color: rgba(255,255,255,.8);

            font-size: 13px;

        }


        .stat-card {

            height: 100%;

            padding: 20px;

            border: 1px solid var(--border);

            border-radius: 14px;

            background: #ffffff;

            box-shadow:
                0 4px 15px
                rgba(15,23,42,.04);

        }


        .stat-icon {

            width: 44px;

            height: 44px;

            border-radius: 11px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: var(--primary-light);

            color: var(--primary);

            font-size: 19px;

        }


        .stat-label {

            margin-top: 17px;

            color: var(--text-muted);

            font-size: 12px;

        }


        .stat-value {

            margin-top: 3px;

            color: var(--text-dark);

            font-size: 25px;

            font-weight: 700;

        }


        .dashboard-section-title {

            margin: 28px 0 14px;

            font-size: 16px;

            font-weight: 700;

        }


        .quick-card {

            display: flex;

            align-items: center;

            gap: 14px;

            height: 100%;

            padding: 17px;

            background: #ffffff;

            border: 1px solid var(--border);

            border-radius: 13px;

            color: var(--text-dark);

            text-decoration: none;

            transition: .2s;

        }


        .quick-card:hover {

            border-color: #b8c9df;

            transform: translateY(-2px);

            box-shadow:
                0 8px 22px
                rgba(15,23,42,.07);

            color: var(--text-dark);

        }


        .quick-icon {

            width: 44px;

            height: 44px;

            flex: 0 0 44px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 11px;

            background: var(--primary-light);

            color: var(--primary);

            font-size: 19px;

        }


        .quick-title {

            font-size: 13px;

            font-weight: 650;

        }


        .quick-description {

            margin-top: 3px;

            color: var(--text-muted);

            font-size: 11px;

        }


        /* =========================================================
           MOBILE CARD ADJUSTMENT
        ========================================================= */

        @media (max-width: 575.98px) {

            .welcome-card {

                padding: 20px;

            }

            .welcome-card h2 {

                font-size: 19px;

            }

            .stat-card {

                padding: 16px;

            }

            .stat-value {

                font-size: 22px;

            }

        }

    </style>

    @stack('styles')

</head>


<body>

<div class="app-wrapper">


    {{-- SIDEBAR --}}

    @include('ai_enhancer.partials.sidebar')


    {{-- MOBILE OVERLAY --}}

    <div
        class="sidebar-overlay"
        id="sidebarOverlay">
    </div>


    {{-- MAIN --}}

    <main class="app-main">


        {{-- TOPBAR --}}

        @include('ai_enhancer.partials.topbar')


        {{-- PAGE CONTENT --}}

        <div class="app-content">

            @yield('content')

        </div>


    </main>

</div>


{{-- Bootstrap JS --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>



<script>

document.addEventListener('DOMContentLoaded', function () {

    const sidebar =
        document.getElementById('appSidebar');

    const overlay =
        document.getElementById('sidebarOverlay');

    const toggle =
        document.getElementById('sidebarToggle');


    function openSidebar() {

        sidebar.classList.add('show');

        overlay.classList.add('show');

        document.body.style.overflow = 'hidden';

    }


    function closeSidebar() {

        sidebar.classList.remove('show');

        overlay.classList.remove('show');

        document.body.style.overflow = '';

    }


    if (toggle) {

        toggle.addEventListener(
            'click',
            function () {

                if (
                    window.innerWidth <= 991
                ) {

                    if (
                        sidebar.classList.contains('show')
                    ) {

                        closeSidebar();

                    } else {

                        openSidebar();

                    }

                }

            }
        );

    }


    if (overlay) {

        overlay.addEventListener(
            'click',
            closeSidebar
        );

    }


    document
        .querySelectorAll('.sidebar-menu a')
        .forEach(function (link) {

            link.addEventListener(
                'click',
                function () {

                    if (
                        window.innerWidth <= 991
                    ) {

                        closeSidebar();

                    }

                }
            );

        });


    window.addEventListener(
        'resize',
        function () {

            if (window.innerWidth > 991) {

                closeSidebar();

            }

        }
    );

});

document.addEventListener('DOMContentLoaded', function () {

    document
        .querySelectorAll('.logout-form')
        .forEach(function (form) {

            form.addEventListener('submit', function (event) {

                const confirmed = confirm(
                    'Are you sure you want to logout?'
                );

                if (!confirmed) {

                    event.preventDefault();

                }

            });

        });

});

</script>


@stack('scripts')

</body>

</html>