<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FB Manager - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/premium.css'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .container {
            width: 100%;
            max-width: 1000px;
            height: 640px;
            background: var(--bg-card);
            border-radius: 32px;
            overflow: hidden;
            display: flex;
            border: 1px solid var(--glass-border);
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
            margin: 0 auto;
        }

        .left-side {
            width: 45%;
            background: linear-gradient(135deg, var(--accent) 0%, #0f172a 100%);
            color: white;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .left-side h1 {
            font-size: 42px;
            margin-bottom: 24px;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .left-side p {
            font-size: 1.1rem;
            line-height: 1.6;
            color: rgba(255,255,255,0.8);
            max-width: 320px;
        }

        .right-side {
            width: 55%;
            padding: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--bg-card);
        }

        .login-box {
            width: 100%;
            max-width: 360px;
        }

        .login-title {
            font-size: 34px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        .login-subtitle {
            color: var(--text-muted);
            margin-bottom: 40px;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 28px;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 32px 0;
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .divider::before, .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--glass-border);
        }

        .divider span { margin: 0 20px; }

        .register-text {
            text-align: center;
            margin-top: 36px;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .register-link {
            text-decoration: none;
            color: var(--accent);
            font-weight: 700;
            margin-left: 6px;
        }

        /* Fix autofill styles */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 30px var(--bg-main) inset !important;
            -webkit-text-fill-color: var(--text-main) !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        @media(max-width: 900px) {
            .container { height: auto; flex-direction: column; border-radius: 20px; }
            .left-side, .right-side { width: 100%; padding: 40px; }
            .left-side { text-align: center; }
            .left-side p { margin: 0 auto; }
        }
    </style>
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 40px 20px;">

    <button class="theme-toggle" onclick="toggleTheme()" id="theme-btn">
        <i data-lucide="moon"></i>
    </button>

    <div class="container">
        <div class="left-side">
            <h1>FB<br><span style="color: var(--bg-main);">Manager</span></h1>
            <p>Your all-in-one platform for professional Facebook management and automation.</p>
        </div>

        <div class="right-side">
            <div class="login-box">
                <h2 class="login-title">Sign In</h2>
                <p class="login-subtitle">Enter your details to continue</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label class="input-label">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus class="premium-input" placeholder="name@example.com">
                        @error('email') <div style="color: #ef4444; font-size: 0.8rem; margin-top: 8px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="input-label">Password</label>
                        <input type="password" name="password" required class="premium-input" placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn-premium" style="width: 100%; margin-top: 8px; padding: 1rem; font-size: 1.1rem; justify-content: center;">Login to Dashboard</button>

                    <div class="divider"><span>OR</span></div>

                    <a href="/auth/facebook" class="btn-facebook" style="width: 100%; justify-content: center;">
                        <i data-lucide="facebook"></i> Continue with Facebook
                    </a>

                    <div class="register-text">
                        Don't have an account? <a href="{{ route('register') }}" class="register-link">Create Account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        
        function toggleTheme() {
            const body = document.body;
            if (body.getAttribute('data-theme') === 'light') {
                body.removeAttribute('data-theme');
                updateIcon('moon');
                localStorage.setItem('theme', 'dark');
            } else {
                body.setAttribute('data-theme', 'light');
                updateIcon('sun');
                localStorage.setItem('theme', 'light');
            }
        }

        function updateIcon(name) {
            const btn = document.getElementById('theme-btn');
            if(btn) {
                btn.innerHTML = `<i data-lucide="${name}"></i>`;
                lucide.createIcons();
            }
        }

        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            document.body.setAttribute('data-theme', 'light');
            updateIcon('sun');
        } else {
            updateIcon('moon');
        }
    </script>
</body>
</html>