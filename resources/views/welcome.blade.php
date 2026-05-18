<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FB Manager - Ultimate Facebook Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/premium.css'])
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; background: radial-gradient(circle at top right, #1e293b, #0f172a);">
    
    <div style="text-align: center; max-width: 800px; padding: 2rem;">
        <div style="font-size: 4rem; font-weight: 800; margin-bottom: 1rem;">
            FB<span style="color: var(--accent);">Manager</span>
        </div>
        <p style="font-size: 1.5rem; color: var(--text-muted); margin-bottom: 3rem;">
            Manage multiple Facebook pages, publish content, and track analytics from one powerful dashboard.
        </p>

        <div style="display: flex; gap: 1.5rem; justify-content: center;">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-premium" style="padding: 1.25rem 3rem; font-size: 1.125rem;">
                    Go to Dashboard <i data-lucide="arrow-right"></i>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-premium" style="padding: 1.25rem 3rem; font-size: 1.125rem;">
                    Get Started <i data-lucide="zap"></i>
                </a>
                <a href="{{ route('register') }}" class="btn-outline" style="padding: 1.25rem 3rem; font-size: 1.125rem;">
                    Register
                </a>
            @endauth
        </div>

        <div style="margin-top: 5rem; display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem;">
            <div>
                <i data-lucide="layers" style="color: var(--accent); margin-bottom: 1rem;"></i>
                <h3 style="margin-bottom: 0.5rem;">Multi-Page Support</h3>
                <p style="color: var(--text-muted); font-size: 0.875rem;">Connect and manage unlimited Facebook pages effortlessly.</p>
            </div>
            <div>
                <i data-lucide="send" style="color: var(--accent); margin-bottom: 1rem;"></i>
                <h3 style="margin-bottom: 0.5rem;">One-Click Publish</h3>
                <p style="color: var(--text-muted); font-size: 0.875rem;">Post text, images, and videos across all your pages instantly.</p>
            </div>
            <div>
                <i data-lucide="bar-chart-2" style="color: var(--accent); margin-bottom: 1rem;"></i>
                <h3 style="margin-bottom: 0.5rem;">Deep Analytics</h3>
                <p style="color: var(--text-muted); font-size: 0.875rem;">Track engagement, likes, and comments with visual reports.</p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
