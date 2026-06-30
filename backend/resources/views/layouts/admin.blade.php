<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark-mode-active': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NagarRakshak Admin Portal')</title>
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Leaflet.js Real Maps CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    
    <style>
        :root {
            --fintech-primary: #2563EB;
            --fintech-primary-hover: #1D4ED8;
            --fintech-dark: #0F172A;
            --fintech-slate: #475569;
            --fintech-bg: #F8FAFC;
            --fintech-card-bg: #FFFFFF;
            --fintech-border: #E2E8F0;
            --fintech-success: #059669;
            --fintech-warning: #D97706;
            --fintech-danger: #E11D48;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background-color: var(--fintech-bg);
            color: var(--fintech-dark);
            transition: background-color 0.3s, color 0.3s;
            letter-spacing: -0.01em;
        }
        
        /* --- Sidebar Styling --- */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #FFFFFF;
            border-right: 1px solid var(--fintech-border);
            z-index: 100;
            padding-top: 1.25rem;
            transition: all 0.3s ease;
            box-shadow: 2px 0 12px rgba(15, 23, 42, 0.02);
        }

        .sidebar-brand {
            padding: 0 1.5rem 1.25rem;
            border-bottom: 1px solid #F1F5F9;
        }

        .sidebar-brand h4 {
            color: var(--fintech-dark);
            font-weight: 800;
            font-size: 1.25rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.02em;
        }

        .sidebar-brand .brand-icon-box {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
        }

        .sidebar-menu {
            padding: 1.25rem 0.75rem;
            list-style: none;
            margin: 0;
        }

        .sidebar-item {
            margin-bottom: 0.35rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.7rem 1rem;
            color: #64748B;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .sidebar-link i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
            color: #94A3B8;
            transition: color 0.2s ease;
        }

        .sidebar-link:hover {
            background-color: #F8FAFC;
            color: var(--fintech-primary);
        }

        .sidebar-link:hover i {
            color: var(--fintech-primary);
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
            color: var(--fintech-primary-hover);
            font-weight: 700;
            box-shadow: inset 3px 0 0 var(--fintech-primary);
        }

        .sidebar-link.active i {
            color: var(--fintech-primary);
        }

        .submenu {
            list-style: none;
            padding-left: 2.3rem;
            margin: 0.25rem 0 0.5rem 0;
        }

        .submenu-link {
            display: block;
            padding: 0.35rem 0;
            color: #64748B;
            text-decoration: none;
            font-size: 0.825rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .submenu-link:hover, .submenu-link.active {
            color: var(--fintech-primary);
            font-weight: 600;
        }

        /* --- Main Content & Layout --- */
        .main-content {
            margin-left: 260px;
            padding: 2rem 2.25rem;
            min-height: 100vh;
        }

        .navbar-custom {
            background-color: #FFFFFF;
            border-bottom: 1px solid var(--fintech-border);
            padding: 0.85rem 2.25rem;
            margin-left: 260px;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
        }

        /* --- Fintech Cards & Containers --- */
        .card-custom {
            background-color: var(--fintech-card-bg);
            border: 1px solid var(--fintech-border);
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.03), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .card-custom:hover {
            box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.07);
        }

        /* --- Badges --- */
        .badge {
            font-weight: 600;
            letter-spacing: 0.02em;
            border-radius: 8px;
        }

        .badge-high {
            background-color: #FFE4E6 !important;
            color: #E11D48 !important;
            border: 1px solid #FECDD3 !important;
        }

        .badge-medium {
            background-color: #FEF3C7 !important;
            color: #B45309 !important;
            border: 1px solid #FDE68A !important;
        }

        .badge-low {
            background-color: #D1FAE5 !important;
            color: #047857 !important;
            border: 1px solid #A7F3D0 !important;
        }

        /* --- Fintech Color Utility Overrides --- */
        .text-green, .text-success {
            color: #059669 !important;
        }

        .bg-green, .bg-success {
            background-color: #059669 !important;
        }

        .text-indigo, .text-primary {
            color: var(--fintech-primary) !important;
        }

        .bg-indigo, .bg-primary {
            background-color: var(--fintech-primary) !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            font-weight: 600;
            border-radius: 10px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1D4ED8 0%, #1E40AF 100%);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
        }

        /* --- Tables --- */
        .table {
            color: var(--fintech-dark);
        }

        .table th {
            background-color: #F8FAFC !important;
            color: #64748B !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            text-uppercase: uppercase;
            letter-spacing: 0.05em;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--fintech-border);
        }

        .table td {
            padding: 0.95rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #F1F5F9;
            font-size: 0.875rem;
        }

        .table-hover tbody tr:hover {
            background-color: #F8FAFC;
        }

        /* --- Dark Mode Styles (Preserved for toggle flexibility) --- */
        .dark-mode-active {
            background-color: #0F172A !important;
            color: #F8FAFC !important;
        }

        .dark-mode-active body {
            background-color: #0F172A !important;
            color: #F8FAFC !important;
        }

        .dark-mode-active .sidebar {
            background-color: #1E293B !important;
            border-right-color: #334155 !important;
        }

        .dark-mode-active .sidebar-brand {
            border-bottom-color: #334155 !important;
        }

        .dark-mode-active .sidebar-link {
            color: #94A3B8 !important;
        }

        .dark-mode-active .sidebar-link:hover {
            background-color: #334155 !important;
            color: #38BDF8 !important;
        }

        .dark-mode-active .sidebar-link.active {
            background: linear-gradient(135deg, #1E3A8A 0%, #1E40AF 100%) !important;
            color: #FFFFFF !important;
        }

        .dark-mode-active .navbar-custom {
            background-color: #1E293B !important;
            border-bottom-color: #334155 !important;
        }

        .dark-mode-active .card-custom {
            background-color: #1E293B !important;
            border-color: #334155 !important;
            color: #F8FAFC !important;
        }

        .dark-mode-active .table {
            color: #F8FAFC !important;
        }

        .dark-mode-active .table th {
            background-color: #0F172A !important;
            color: #94A3B8 !important;
        }

        .dark-mode-active .list-group-item {
            background-color: #1E293B !important;
            color: #F8FAFC !important;
            border-color: #334155 !important;
        }
        
        .dark-mode-active .input-group-text, 
        .dark-mode-active .form-control, 
        .dark-mode-active .form-select {
            background-color: #0F172A !important;
            border-color: #334155 !important;
            color: #F8FAFC !important;
        }

        .dark-mode-active .text-dark,
        .dark-mode-active .text-dark-emphasis {
            color: #F8FAFC !important;
        }
        
        .dark-mode-active .text-muted {
            color: #94A3B8 !important;
        }
        
        .dark-mode-active .bg-white {
            background-color: #1E293B !important;
            color: #F8FAFC !important;
        }
        
        .dark-mode-active .bg-light {
            background-color: #334155 !important;
            color: #F8FAFC !important;
        }

        .dark-mode-active .dropdown-menu {
            background-color: #1E293B !important;
            border-color: #334155 !important;
        }

        .dark-mode-active .dropdown-item {
            color: #F8FAFC !important;
        }

        .dark-mode-active .dropdown-item:hover {
            background-color: #334155 !important;
            color: #38BDF8 !important;
        }
    </style>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h4>
                <div class="brand-icon-box">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <span>Nagar<span style="color: var(--fintech-primary);">Rakshak</span></span>
            </h4>
            <small class="text-muted text-uppercase tracking-wider font-weight-bold" style="font-size: 0.65rem; margin-top: 6px; display: block; letter-spacing: 0.08em;">Fintech Intelligence Portal</small>
        </div>
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i> Dashboard
                </a>
            </li>
            
            <li class="sidebar-item">
                <a href="{{ route('admin.cases.index') }}" class="sidebar-link {{ Route::is('admin.cases.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-triangle-exclamation"></i> Cases Registry
                </a>
                <ul class="submenu">
                    <li><a href="{{ route('admin.cases.index') }}" class="submenu-link">All Cases</a></li>
                    <li><a href="{{ route('admin.cases.index', ['status' => 'Pending']) }}" class="submenu-link">Pending Review</a></li>
                    <li><a href="{{ route('admin.cases.index', ['severity' => 'High Risk']) }}" class="submenu-link">Critical Severity</a></li>
                    <li><a href="{{ route('admin.cases.index', ['status' => 'Resolved']) }}" class="submenu-link">Resolved Cases</a></li>
                </ul>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('admin.maps.index') }}" class="sidebar-link {{ Route::is('admin.maps.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-map-location-dot"></i> Geo Map Center
                </a>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ Route::is('admin.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> User Accounts
                </a>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('admin.ai.dashboard') }}" class="sidebar-link {{ Route::is('admin.ai.*') || Route::is('admin.ai-settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-brain"></i> AI Intelligence Hub
                </a>
                <ul class="submenu">
                    <li><a href="{{ route('admin.ai.dashboard') }}" class="submenu-link">Monitoring Dashboard</a></li>
                    <li><a href="{{ route('admin.ai.logs') }}" class="submenu-link">Analysis Logs</a></li>
                    <li><a href="{{ route('admin.ai-settings.index') }}" class="submenu-link">Configuration Settings</a></li>
                </ul>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('admin.notifications.index') }}" class="sidebar-link {{ Route::is('admin.notifications.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bell"></i> Broadcasts & Alerts
                </a>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('admin.analytics.index') }}" class="sidebar-link {{ Route::is('admin.analytics.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> Performance Analytics
                </a>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('admin.logs.index') }}" class="sidebar-link {{ Route::is('admin.logs.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice"></i> Audit Logs
                </a>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('admin.health.index') }}" class="sidebar-link {{ Route::is('admin.health.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-heart-pulse"></i> System Telemetry
                </a>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ Route::is('admin.settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i> System Settings
                </a>
            </li>

            <li class="sidebar-item">
                <a href="{{ route('admin.dev-docs') }}" class="sidebar-link {{ Route::is('admin.dev-docs') ? 'active' : '' }}">
                    <i class="fa-solid fa-code"></i> Developer Portal
                </a>
            </li>

            <li class="sidebar-item">
                <a href="/municipality/dashboard" class="sidebar-link" target="_blank">
                    <i class="fa-solid fa-building-flag"></i> Municipal Panel
                </a>
            </li>
        </ul>
    </div>

    <!-- Top Navbar -->
    <nav class="navbar navbar-custom navbar-expand-lg sticky-top">
        <div class="container-fluid justify-content-between">
            <!-- Search bar -->
            <div class="d-flex align-items-center">
                <form action="{{ route('admin.cases.index') }}" method="GET" class="d-none d-md-flex">
                    <div class="input-group input-group-sm" style="width: 280px;">
                        <span class="input-group-text bg-light border-0 ps-3 rounded-start-pill"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="location" class="form-control bg-light border-0 rounded-end-pill ps-2" placeholder="Search incidents, locations...">
                    </div>
                </form>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <!-- AI Status Indicator -->
                <div class="d-flex align-items-center gap-2 bg-light rounded-pill px-3 py-1 border" style="font-size: 0.75rem;">
                    <span class="rounded-circle bg-success" style="width: 8px; height: 8px; display: inline-block; box-shadow: 0 0 8px rgba(5, 150, 105, 0.5);"></span>
                    <span class="text-secondary fw-semibold">Gemini Intelligence: Active</span>
                </div>

                <!-- Dark Mode Toggler -->
                <button class="btn btn-light rounded-circle border" type="button" style="width: 40px; height: 40px;" 
                        @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" title="Toggle Theme Mode">
                    <i class="fa-solid" :class="darkMode ? 'fa-sun text-warning' : 'fa-moon text-secondary'"></i>
                </button>

                <!-- Notifications dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light border position-relative rounded-circle" type="dropdown" style="width: 40px; height: 40px;" data-bs-toggle="dropdown">
                        <i class="fa-regular fa-bell text-secondary"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.55rem;">2</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-3 shadow border-0" style="width: 300px; border-radius: 14px;">
                        <h6 class="fw-bold mb-2 text-dark">System Alerts</h6>
                        <li>
                            <a href="{{ route('admin.cases.index') }}" class="text-decoration-none d-block p-2 rounded-3 hover-bg-light border-bottom mb-1">
                                <small class="fw-bold d-block text-dark">⚠️ Critical Pothole Reported</small>
                                <small class="text-muted">High priority report logged in Mahaveer Nagar</small>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.cases.index') }}" class="text-decoration-none d-block p-2 rounded-3 hover-bg-light">
                                <small class="fw-bold d-block text-dark">🤖 AI Scan Complete</small>
                                <small class="text-muted">Hazard #1 processed by Gemini API</small>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Profile Dropdown -->
                <div class="dropdown">
                    <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown" style="cursor: pointer;">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: 700; background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%); box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);">
                            CA
                        </div>
                        <div class="d-none d-sm-block">
                            <span class="d-block font-weight-bold text-dark" style="font-size: 0.85rem; font-weight:700;">Chief Administrator</span>
                            <small class="text-muted" style="font-size: 0.7rem; font-weight: 500;">Municipal Security Command</small>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 14px;">
                        <li><a class="dropdown-item py-2" href="{{ route('admin.settings.index') }}"><i class="fa-solid fa-user-gear me-2 text-muted"></i> Account Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger" href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa-solid fa-right-from-bracket me-2"></i> Sign Out
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert" style="background-color: #D1FAE5; color: #047857;">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- JS Scripts -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- Leaflet.js Real Maps JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    @yield('scripts')
</body>
</html>
