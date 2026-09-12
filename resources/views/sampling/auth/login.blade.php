<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sampling Portal Login | Fashion ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #1e3a5f;
            --brand-accent: #c98a4b;
            --brand-bg: #f8fafc;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            width: 100%;
            max-width: 960px;
        }
        .login-sidebar {
            background: linear-gradient(160deg, #1e3a5f 0%, #0f172a 100%);
            color: #ffffff;
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }
        .login-sidebar::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at top right, rgba(201, 138, 75, 0.15), transparent 70%);
            pointer-events: none;
        }
        .brand-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .login-form-pane {
            padding: 50px 45px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.95rem;
            border: 1.5px solid #e2e8f0;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: var(--brand-accent);
            box-shadow: 0 0 0 4px rgba(201, 138, 75, 0.15);
        }
        .btn-brand {
            background: linear-gradient(135deg, #1e3a5f 0%, #2b5288 100%);
            color: #ffffff;
            border: none;
            padding: 13px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.3px;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .btn-brand:hover {
            background: linear-gradient(135deg, #162c46 0%, #1f3f6b 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(30, 58, 95, 0.3);
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
            font-size: 0.92rem;
            color: #cbd5e1;
        }
        .feature-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(201, 138, 75, 0.2);
            color: var(--brand-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="row g-0">
            <!-- Left Info Panel -->
            <div class="col-lg-5 login-sidebar d-none d-lg-flex">
                <div>
                    <div class="d-inline-flex align-items-center gap-2 mb-4 px-3 py-1 rounded-pill" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);">
                        <i class="bi bi-palette text-warning"></i>
                        <span class="small fw-semibold text-white tracking-wide">FASHION ERP &bull; SAMPLING</span>
                    </div>
                    <h2 class="brand-title display-6 mb-3 text-white">Artisan & Design Studio</h2>
                    <p class="text-white-50 small mb-4">Managing product development from raw design concept through physical prototypes, voice notes, and approved Frozen Sample masters.</p>

                    <div class="mt-4">
                        <div class="feature-item">
                            <div class="feature-icon"><i class="bi bi-diagram-3"></i></div>
                            <span>Projects, Batches & Sample Tracking</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="bi bi-mic"></i></div>
                            <span>In-Browser Voice Recorded Production Notes</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="bi bi-calculator"></i></div>
                            <span>Live BOM, Operations & Direct Costing</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                            <span>Immutable Frozen Revisions for Production</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-top border-secondary border-opacity-25 small text-white-50">
                    &copy; {{ date('Y') }} Fashion ERP. All rights reserved.
                </div>
            </div>

            <!-- Right Form Panel -->
            <div class="col-lg-7 login-form-pane">
                <div class="mb-4">
                    <h3 class="fw-bold text-dark mb-1">Sampling Portal Sign In</h3>
                    <p class="text-muted small">Please enter your company email and password to access your studio workspace.</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>{{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('sampling.login.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="name@samplingcompany.com" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label fw-semibold small text-dark">Password</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 ps-0" placeholder="Enter your password" required>
                            <button type="button" class="input-group-text bg-white border-start-0 text-muted" onclick="togglePassword()">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small text-muted" for="remember">Keep me logged in</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 mb-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In to Studio Workspace
                    </button>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            Need access or forgot your credentials? Contact your system Administrator.
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pass = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (pass.type === 'password') {
                pass.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                pass.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }
    </script>
</body>
</html>
