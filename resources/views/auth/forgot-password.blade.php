<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Manager - Reset Password</title>
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
            height: 100vh;
            background-color: var(--bg);
            background-image: radial-gradient(at 0% 0%, var(--accent-glow) 0px, transparent 50%);
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
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
            max-width: 500px;
            background: var(--card-bg);
            border-radius: 32px;
            padding: 60px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--border);
            position: relative;
            text-align: center;
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: var(--input-bg);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 32px;
            color: var(--accent);
        }

        .forgot-title {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        .forgot-subtitle {
            color: var(--text-muted);
            margin-bottom: 40px;
            font-size: 1rem;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 28px;
            text-align: left;
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

        .submit-btn {
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
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(37, 99, 235, 0.5);
        }

        .success-message {
            background: #dcfce7;
            color: #166534;
            padding: 16px;
            border-radius: 16px;
            margin-bottom: 32px;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .back-link {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 32px;
            transition: 0.2s;
        }

        .back-link:hover {
            color: var(--accent);
        }
    </style>
</head>
<body>
    <button class="theme-toggle" onclick="toggleTheme()" id="theme-btn">
        <i data-lucide="moon"></i>
    </button>

    <div class="container">
        <div class="icon-box">
            <i data-lucide="key-round" size="40"></i>
        </div>

        <h1 class="forgot-title">Reset Password</h1>
        <p class="forgot-subtitle">No worries! Enter your email and we'll send you instructions to reset your password.</p>

        @if (session('status'))
            <div class="success-message">
                <i data-lucide="check-circle" size="18" style="vertical-align: middle; margin-right: 8px;"></i>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-input" placeholder="name@example.com">
                @error('email') <div style="color: #ef4444; font-size: 0.8rem; margin-top: 8px;">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="submit-btn">Send Reset Link</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            <i data-lucide="arrow-left" size="18"></i> Back to Login
        </a>
    </div>

    <script>
        lucide.createIcons();
        
        function toggleTheme() {
            const body = document.body;
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

        const savedTheme = localStorage.getItem('auth-theme');
        if (savedTheme === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
            updateIcon('sun');
        }
    </script>
</body>
</html>