<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark-mode-active': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NagarRakshak Admin Portal</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 10% 20%, rgb(240, 253, 244) 0%, rgb(248, 250, 252) 90%);
            color: #0F172A;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s, color 0.3s;
            padding: 1.5rem;
        }

        .login-card {
            background-color: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
            transition: all 0.3s ease;
        }

        .login-brand h3 {
            color: #16A34A;
            font-weight: 800;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .form-control-custom {
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            background-color: #F8FAFC;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            background-color: #ffffff;
            border-color: #16A34A;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.1);
            outline: none;
        }

        .btn-green-auth {
            background-color: #16A34A;
            border-color: #16A34A;
            color: white;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-green-auth:hover {
            background-color: #15803D;
            border-color: #15803D;
            color: white;
            transform: translateY(-1px);
        }

        .btn-green-auth:active {
            transform: translateY(1px);
        }

        /* Password input positioning */
        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #64748B;
            cursor: pointer;
            z-index: 10;
        }

        .dark-mode-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        /* --- Dark Mode Styles --- */
        .dark-mode-active {
            background: radial-gradient(circle at 10% 20%, rgb(15, 23, 42) 0%, rgb(9, 15, 29) 90%) !important;
            color: #F8FAFC !important;
        }

        .dark-mode-active .login-card {
            background-color: #1E293B !important;
            border-color: #334155 !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4) !important;
        }

        .dark-mode-active .form-control-custom {
            background-color: #0F172A !important;
            border-color: #334155 !important;
            color: #F8FAFC !important;
        }

        .dark-mode-active .form-control-custom:focus {
            background-color: #0F172A !important;
            border-color: #22C55E !important;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.15) !important;
        }

        .dark-mode-active .text-dark {
            color: #F8FAFC !important;
        }

        .dark-mode-active .text-muted {
            color: #94A3B8 !important;
        }

        .dark-mode-active .btn-light {
            background-color: #1E293B !important;
            border-color: #334155 !important;
            color: #F8FAFC !important;
        }

        /* ── Hackathon Demo Showcase Card ── */
        .demo-card {
            width: 100%;
            max-width: 440px;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #E2E8F0;
            background: linear-gradient(135deg, #ffffff 0%, #F0FDF4 100%);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
        }

        .demo-card-header {
            background: linear-gradient(135deg, #16A34A 0%, #059669 100%);
            color: white;
            padding: 0.7rem 1.2rem;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .demo-card-body {
            padding: 0.8rem 1rem;
        }

        .cred-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.7rem 0.8rem;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        .cred-row:hover {
            background: rgba(22, 163, 74, 0.06);
            border-color: rgba(22, 163, 74, 0.15);
            transform: translateX(4px);
        }

        .cred-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .admin-icon {
            background: linear-gradient(135deg, #16A34A 0%, #059669 100%);
            color: white;
        }

        .cred-info {
            flex: 1;
            min-width: 0;
        }
        .cred-role {
            font-weight: 700;
            font-size: 0.85rem;
            color: #0F172A;
        }
        .cred-email {
            font-size: 0.72rem;
            color: #64748B;
            font-family: 'Courier New', monospace;
        }

        .cred-pass code {
            background: #F1F5F9;
            color: #16A34A;
            padding: 3px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid #E2E8F0;
        }

        .cred-action {
            color: #16A34A;
            font-size: 1rem;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .cred-row:hover .cred-action {
            opacity: 1;
        }

        .demo-card-footer {
            padding: 0.7rem 1.2rem 1rem;
            border-top: 1px solid #E2E8F0;
        }

        .tech-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 10px;
        }

        .tech-badge {
            font-size: 0.65rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            letter-spacing: 0.3px;
        }
        .tech-badge.laravel { background: #FEF2F2; color: #DC2626; }
        .tech-badge.android { background: #F0FDF4; color: #16A34A; }
        .tech-badge.ai { background: #EFF6FF; color: #2563EB; }
        .tech-badge.gcp { background: #FFF7ED; color: #EA580C; }
        .tech-badge.firebase { background: #FFFBEB; color: #D97706; }

        .demo-tagline {
            font-size: 0.72rem;
            color: #64748B;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Dark mode for demo card */
        .dark-mode-active .demo-card {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%) !important;
            border-color: #334155 !important;
        }
        .dark-mode-active .cred-role { color: #F8FAFC !important; }
        .dark-mode-active .cred-email { color: #94A3B8 !important; }
        .dark-mode-active .cred-pass code {
            background: #0F172A !important;
            border-color: #334155 !important;
        }
        .dark-mode-active .demo-card-footer { border-top-color: #334155 !important; }
        .dark-mode-active .cred-row:hover {
            background: rgba(34, 197, 94, 0.08) !important;
            border-color: rgba(34, 197, 94, 0.2) !important;
        }
        .dark-mode-active .demo-tagline { color: #94A3B8 !important; }
        .dark-mode-active .tech-badge.laravel { background: rgba(220,38,38,0.12) !important; }
        .dark-mode-active .tech-badge.android { background: rgba(22,163,74,0.12) !important; }
        .dark-mode-active .tech-badge.ai { background: rgba(37,99,235,0.12) !important; }
        .dark-mode-active .tech-badge.gcp { background: rgba(234,88,12,0.12) !important; }
        .dark-mode-active .tech-badge.firebase { background: rgba(217,119,6,0.12) !important; }
    </style>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>

    <!-- Dark Mode Switcher -->
    <div class="dark-mode-toggle">
        <button class="btn btn-light rounded-circle shadow-sm" type="button" style="width: 42px; height: 42px;" 
                @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)">
            <i class="fa-solid" :class="darkMode ? 'fa-sun text-warning' : 'fa-moon'"></i>
        </button>
    </div>

    <div class="d-flex flex-column align-items-center" style="width: 100%; max-width: 480px;">

        <!-- Login Container -->
        <div class="login-card">
            <div class="login-brand text-center mb-4">
                <h3><i class="fa-solid fa-shield-halved"></i> NagarRakshak</h3>
                <span class="text-muted text-uppercase tracking-wider fw-bold d-block mt-2" style="font-size: 0.65rem; letter-spacing: 1px;">Admin Dashboard</span>
            </div>

            @if($errors->any())
                <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4" role="alert">
                    <ul class="mb-0 ps-3" style="font-size: 0.825rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" id="loginForm" x-data="{ showPassword: false, loading: false }" @submit="loading = true">
                @csrf
                
                <!-- Email Input -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold text-muted" style="font-size: 0.8rem;">Email Address</label>
                    <input type="email" name="email" id="email" 
                           class="form-control form-control-custom @error('email') is-invalid @enderror" 
                           value="admin@nagarrakshak.org" required autofocus placeholder="admin@nagarrakshak.org">
                </div>

                <!-- Password Input -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label fw-semibold text-muted mb-0" style="font-size: 0.8rem;">Password</label>
                    </div>
                    <div class="password-container">
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password" 
                               class="form-control form-control-custom @error('password') is-invalid @enderror" 
                               required placeholder="••••••••" value="password">
                        <button type="button" class="password-toggle" @click="showPassword = !showPassword">
                            <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checked -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" checked style="border-radius: 4px; cursor: pointer;">
                        <label class="form-check-label text-muted" for="remember" style="font-size: 0.85rem; cursor: pointer; user-select: none;">
                            Keep me signed in
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-green-auth d-flex align-items-center justify-content-center gap-2" :disabled="loading">
                        <span x-show="!loading">Sign In</span>
                        <span x-show="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span x-show="loading">Authenticating...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Hackathon Demo Showcase Card -->
        <div class="demo-card mt-4">
            <div class="demo-card-header">
                <i class="fa-solid fa-trophy"></i>
                <span>Hackathon Demo Credentials</span>
            </div>
            <div class="demo-card-body">
                <div class="cred-row" onclick="fillCredentials('admin@nagarrakshak.org', 'password')">
                    <div class="cred-icon admin-icon"><i class="fa-solid fa-user-shield"></i></div>
                    <div class="cred-info">
                        <div class="cred-role">City Admin</div>
                        <div class="cred-email">admin@nagarrakshak.org</div>
                    </div>
                    <div class="cred-pass"><code>password</code></div>
                    <div class="cred-action"><i class="fa-solid fa-arrow-right-to-bracket"></i></div>
                </div>
            </div>
            <div class="demo-card-footer">
                <div class="tech-badges">
                    <span class="tech-badge laravel"><i class="fa-brands fa-laravel"></i> Laravel 11</span>
                    <span class="tech-badge android"><i class="fa-brands fa-android"></i> Kotlin</span>
                    <span class="tech-badge ai"><i class="fa-solid fa-brain"></i> Gemini AI</span>
                    <span class="tech-badge gcp"><i class="fa-brands fa-google"></i> GCP</span>
                    <span class="tech-badge firebase"><i class="fa-solid fa-bell"></i> FCM</span>
                </div>
                <div class="demo-tagline">
                    <i class="fa-solid fa-heart" style="color: #EF4444;"></i>
                    Built for safer cities — spot hazards, save lives.
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function fillCredentials(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
            // Subtle pulse effect
            document.querySelector('.login-card').style.boxShadow = '0 0 0 4px rgba(22, 163, 74, 0.25)';
            setTimeout(() => {
                document.querySelector('.login-card').style.boxShadow = '';
            }, 600);
        }
    </script>
</body>
</html>

