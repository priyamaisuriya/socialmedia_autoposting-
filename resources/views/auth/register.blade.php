<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Manager - Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --input-bg: #f1f5f9;
            --accent: #2563eb;
            --accent-glow: rgba(37, 99, 235, 0.1);
        }

        [data-theme="dark"] {
            --bg: #0a0e17;
            --card-bg: #161b2a;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(255, 255, 255, 0.08);
            --input-bg: rgba(255, 255, 255, 0.03);
            --accent: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background-color: var(--bg);
            background-image: radial-gradient(at 0% 0%, var(--accent-glow) 0px, transparent 50%),
                              radial-gradient(at 100% 100%, var(--accent-glow) 0px, transparent 50%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            color: var(--text-main);
        }

        .theme-toggle {
            position: absolute;
            top: 2rem;
            right: 2.5rem;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: var(--card-bg);
            border: 1px solid var(--border);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 100;
        }

        .theme-toggle:hover {
            transform: scale(1.05);
            border-color: var(--accent);
        }

        .container {
            width: 100%;
            max-width: 1000px;
            background: var(--card-bg);
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            display: flex;
            border: 1px solid var(--border);
            position: relative;
        }

        /* Left Side */
        .left-side {
            width: 45%;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            border-right: 1px solid var(--border);
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
            color: #94a3b8;
            max-width: 320px;
        }

        /* Right Side */
        .right-side {
            width: 55%;
            padding: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--card-bg);
        }

        .register-box {
            width: 100%;
            max-width: 380px;
        }

        .register-title {
            font-size: 34px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        .register-subtitle {
            color: var(--text-muted);
            margin-bottom: 40px;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid var(--border);
            border-radius: 16px;
            font-size: 1rem;
            background: var(--input-bg);
            color: var(--text-main);
            outline: none;
        }

        .form-input:focus {
            border-color: var(--accent);
            background: var(--card-bg);
            box-shadow: 0 0 0 4px var(--accent-glow);
        }

        .register-btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
            margin-top: 8px;
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(37, 99, 235, 0.5);
        }

        .login-text {
            text-align: center;
            margin-top: 36px;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .login-link {
            text-decoration: none;
            color: var(--accent);
            font-weight: 700;
            margin-left: 6px;
        }

        @media(max-width: 900px) {
            .container { flex-direction: column; border-radius: 20px; }
            .left-side, .right-side { width: 100%; padding: 40px; }
            .left-side { text-align: center; }
            .left-side p { margin: 0 auto; }
        }
    </style>
</head>
<body>
    <button class="theme-toggle" onclick="toggleTheme()" id="theme-btn">
        <i data-lucide="moon"></i>
    </button>

    <div class="container">
        <div class="left-side">
            <h1>Start Your<br>Journey</h1>
            <p>Join thousands of professionals managing their social presence with ease.</p>
        </div>

        <div class="right-side">
            <div class="register-box">
                <h2 class="register-title">Create Account</h2>
                <p class="register-subtitle">Get started for free today</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus class="form-input" placeholder="John Doe">
                        @error('name') <div style="color: #ef4444; font-size: 0.8rem; margin-top: 8px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="form-input" placeholder="name@example.com">
                        @error('email') <div style="color: #ef4444; font-size: 0.8rem; margin-top: 8px;">{{ $message }}</div> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" required class="form-input" placeholder="••••••••">
                            @error('password') <div style="color: #ef4444; font-size: 0.8rem; margin-top: 8px;">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirm</label>
                            <input type="password" name="password_confirmation" required class="form-input" placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="register-btn">Create Account</button>

                    <div class="login-text">
                        Already have an account? <a href="{{ route('login') }}" class="login-link">Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        
        function toggleTheme() {
            const body = document.body;
            const btn = document.getElementById('theme-btn');
            
            if (body.getAttribute('data-theme') === 'dark') {
                body.removeAttribute('data-theme');
                updateIcon('moon');
                localStorage.setItem('auth-theme', 'light');
            } else {
                body.setAttribute('data-theme', 'dark');
                updateIcon('sun');
                localStorage.setItem('auth-theme', 'dark');
            }
        }

        function updateIcon(name) {
            const btn = document.getElementById('theme-btn');
            btn.innerHTML = `<i data-lucide="${name}"></i>`;
            lucide.createIcons();
        }

        // Initialize theme
        const savedTheme = localStorage.getItem('auth-theme');
        if (savedTheme === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
            updateIcon('sun');
        }
    </script>
</body>
</html>