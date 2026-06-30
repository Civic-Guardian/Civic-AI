<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kota Municipal Authority Panel')</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --municipal-primary: #10B981;
            --municipal-primary-hover: #059669;
            --municipal-dark: #0F172A;
            --municipal-bg: #F8FAFC;
            --municipal-border: #E2E8F0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--municipal-bg);
            color: var(--municipal-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-municipal {
            background-color: #FFFFFF;
            border-bottom: 1px solid var(--municipal-border);
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);
        }

        .brand-logo {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
        }

        .card-custom {
            background-color: #FFFFFF;
            border: 1px solid var(--municipal-border);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.015);
        }

        .nav-link-custom {
            color: #64748B;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .nav-link-custom:hover, .nav-link-custom.active {
            color: var(--municipal-primary) !important;
        }
    </style>
</head>
<body>

    <!-- Municipal Navbar -->
    <nav class="navbar navbar-expand-lg navbar-municipal py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('municipality.dashboard') }}">
                <div class="brand-logo"><i class="fa-solid fa-city fa-xs"></i></div>
                <span>Kota Municipal Authority</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#municipalNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="municipalNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-2">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ Request::is('municipality/dashboard') ? 'active' : '' }}" href="{{ route('municipality.dashboard') }}"><i class="fa-solid fa-gauge me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ Request::is('municipality/cases*') ? 'active' : '' }}" href="{{ route('municipality.cases.index') }}"><i class="fa-solid fa-clipboard-list me-1"></i> Assigned Cases</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-secondary small"><i class="fa-solid fa-user-tie me-1"></i> Ward Officer Panel</span>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary border-secondary rounded-pill px-3"><i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Admin Portal</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="flex-grow-1 py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-3 text-center text-secondary small mt-auto">
        <div class="container">
            &copy; 2026 Kota Municipal Corporation &bull; NagarRakshak Internal Department Tool
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
