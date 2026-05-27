<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FB Manager') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Custom Premium DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('css/premium-datatables.css') }}">

    <!-- Styles -->
    <style>
        :root {
            --bg-main: #f8fafc;
            --sidebar-bg: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(0, 0, 0, 0.05);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --accent: #2563eb;
            --accent-glow: rgba(37, 99, 235, 0.15);
            --card-bg: #ffffff;
            --nav-active: #f1f5f9;
        }

        [data-theme="dark"] {
            --bg-main: #05080f;
            --sidebar-bg: #0c111d;
            --glass-bg: rgba(12, 17, 29, 0.7);
            --glass-border: rgba(255, 255, 255, 0.05);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.3);
            --card-bg: #0c111d;
            --nav-active: rgba(255, 255, 255, 0.03);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-main); 
            color: var(--text-main); 
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background-image: radial-gradient(circle at 10% 20%, var(--accent-glow) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, var(--accent-glow) 0%, transparent 40%);
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 280px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--glass-border);
            padding: 2.5rem 1.5rem;
            z-index: 100;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }

        .sidebar.collapsed { width: 90px; padding: 2.5rem 1rem; }
        .sidebar.collapsed .logo-text, .sidebar.collapsed span { display: none; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            padding: 1rem 1.25rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 16px;
            margin-bottom: 0.75rem;
            font-weight: 600;
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }

        .nav-link:hover {
            color: var(--accent);
            background: var(--nav-active);
            transform: translateX(5px);
        }

        .nav-link.active {
            background: var(--accent);
            color: white;
            box-shadow: 0 10px 20px var(--accent-glow);
        }

        .top-header {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            left: calc(280px + 1.5rem);
            height: 80px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            z-index: 90;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .top-header.expanded { left: calc(90px + 1.5rem); }

        .main-content {
            margin-left: 280px;
            padding: 130px 2.5rem 2.5rem;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeInUp 0.6s ease-out;
            position: relative;
            z-index: 1;
        }

        .main-content.expanded { margin-left: 90px; }

        .premium-card {
            background: var(--card-bg);
            border-radius: 30px;
            padding: 2.5rem;
            border: 1px solid var(--glass-border);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.03);
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .premium-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.08);
            border-color: var(--accent);
        }

        .premium-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top right, var(--accent-glow), transparent);
            opacity: 0;
            transition: 0.4s;
            pointer-events: none !important;
        }

        .premium-card:hover::before { opacity: 1; }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            border: none;
            cursor: pointer;
            box-shadow: 0 10px 20px var(--accent-glow);
            transition: 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 30px var(--accent-glow);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-danger:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.2);
        }

        .alert {
            padding: 1.25rem 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            font-weight: 600;
            animation: fadeInUp 0.4s ease-out;
        }
        .alert-success { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
        .alert-error { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }

        .theme-toggle {
            background: var(--nav-active);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            width: 50px;
            height: 50px;
            border-radius: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }

        .theme-toggle:hover {
            background: var(--accent);
            color: white;
            transform: rotate(15deg);
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div style="margin-bottom: 4rem; padding: 0 1rem; animation: float 6s ease-in-out infinite;">
            <div class="logo-text" style="font-size: 1.75rem; font-weight: 900; letter-spacing: -0.04em;">
                Social<span style="color: var(--accent);">Poster</span>
            </div>
        </div>

        <nav>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('facebook.index') }}" class="nav-link {{ request()->routeIs('facebook.index') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; flex-shrink: 0;"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg> <span>Facebook Accounts</span>
            </a>
            <a href="{{ route('instagram.index') }}" class="nav-link {{ request()->routeIs('instagram.index') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; flex-shrink: 0;"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg> <span>Instagram Accounts</span>
            </a>
            <a href="{{ route('pages.index') }}" class="nav-link {{ request()->routeIs('pages.index') ? 'active' : '' }}">
                <i data-lucide="layers"></i> <span>My Pages</span>
            </a>
            
            <!-- My Posts Hub -->
            <a href="{{ route('posts.index') }}" class="nav-link {{ request()->routeIs('posts.index', 'posts.show') ? 'active' : '' }}">
                <i data-lucide="grid"></i>
                <span>My Posts Hub</span>
            </a>

            <a href="{{ route('stories.index') }}" class="nav-link {{ request()->routeIs('stories.index') ? 'active' : '' }}">
                <i data-lucide="history"></i> <span>My Stories</span>
            </a>
            <a href="{{ route('analytics.index') }}" class="nav-link {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
                <i data-lucide="calendar"></i> <span>Content Planner</span>
            </a>
            
            <!-- WhatsApp Module -->
            <a href="{{ route('whatsapp.create') }}" class="nav-link {{ request()->routeIs('whatsapp.*') ? 'active' : '' }}">
                <i data-lucide="message-circle"></i> <span>WhatsApp Connect</span>
            </a>
            <a href="{{ route('ads.index') }}" class="nav-link {{ request()->routeIs('ads.*') ? 'active' : '' }}">
                <i data-lucide="megaphone"></i> <span>Ads Manager</span>
            </a>
        </nav>
    </div>

    <header class="top-header" id="header">
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <button class="theme-toggle" id="toggle-btn">
                <i data-lucide="menu"></i>
            </button>
            <div style="font-weight: 700; font-size: 1.1rem; letter-spacing: -0.01em;">Overview</div>
        </div>
        
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <button class="theme-toggle" id="theme-toggle">
                <i data-lucide="moon" id="moon-icon"></i>
                <i data-lucide="sun" id="sun-icon" style="display: none;"></i>
            </button>
            
            <div style="display: flex; align-items: center; gap: 1rem; padding-left: 1.5rem; border-left: 2px solid var(--glass-border);">
                <div style="text-align: right;">
                    <div style="font-weight: 800; font-size: 0.9375rem;">{{ Auth::user()->name }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">PRO MEMBER</div>
                </div>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" style="margin: 0; display: inline;">
                    @csrf
                    <button type="button" onclick="document.getElementById('logout-form').submit();" class="theme-toggle" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: none; cursor: pointer;" title="Logout">
                        <i data-lucide="log-out" style="pointer-events: none;"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="main-content" id="main">
        @if(session('success'))
            <div class="alert alert-success">
                <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error">
                <i data-lucide="alert-circle" style="width: 18px; height: 18px;"></i>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        lucide.createIcons();

        const sidebar = document.getElementById('sidebar');
        const header = document.getElementById('header');
        const main = document.getElementById('main');
        const toggleBtn = document.getElementById('toggle-btn');
        
        // Sidebar Toggle Logic
        const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
            header.classList.add('expanded');
            main.classList.add('expanded');
        }

        toggleBtn.addEventListener('click', () => {
            const nowCollapsed = sidebar.classList.toggle('collapsed');
            header.classList.toggle('expanded');
            main.classList.toggle('expanded');
            localStorage.setItem('sidebar-collapsed', nowCollapsed);
        });

        // Theme Toggle Logic
        const themeToggle = document.getElementById('theme-toggle');
        const sunIcon = document.getElementById('sun-icon');
        const moonIcon = document.getElementById('moon-icon');
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateIcons(savedTheme);

        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcons(newTheme);
        });

        function updateIcons(theme) {
            if (theme === 'light') {
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
            } else {
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
            }
        }
    </script>
    @stack('modals')
    @stack('scripts')
</body>
</html>
