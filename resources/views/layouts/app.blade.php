<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title')</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial,sans-serif;
        }

        body{
            background:#f4f7fb;
            display:flex;
            min-height:100vh;
            transition:0.3s;
        }

        body.dark{
            background:#111827;
        }

        /* Sidebar */

        .sidebar{
            width:260px;
            height:100vh;
            background:linear-gradient(180deg,#4f46e5,#9333ea);
            color:white;
            padding:30px 20px;
            position:fixed;
            left:0;
            top:0;
            overflow:auto;
        }

        .logo{
            font-size:30px;
            font-weight:bold;
            margin-bottom:40px;
            text-align:center;
            letter-spacing:1px;
        }

        .menu a{
            display:block;
            color:white;
            text-decoration:none;
            padding:15px 18px;
            border-radius:12px;
            margin-bottom:14px;
            transition:0.3s;
            font-size:15px;
            font-weight:500;
        }

        .menu a:hover{
            background:rgba(255,255,255,0.15);
        }

        /* Main */

        .main{
            margin-left:260px;
            width:calc(100% - 260px);
            min-height:100vh;
            padding:30px;
        }

        /* Topbar */

        .topbar{
            width:100%;
            background:white;
            padding:22px 25px;
            border-radius:18px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            box-shadow:0 5px 20px rgba(0,0,0,0.08);
            margin-bottom:30px;
            transition:0.3s;
        }

        body.dark .topbar{
            background:#1f2937;
        }

        .welcome h2{
            color:#111827;
            margin-bottom:6px;
            font-size:28px;
        }

        body.dark .welcome h2{
            color:white;
        }

        .welcome p{
            color:#6b7280;
            font-size:15px;
        }

        body.dark .welcome p{
            color:#d1d5db;
        }

        /* Buttons */

        .top-actions{
            display:flex;
            align-items:center;
            gap:12px;
        }

        .theme-btn{
            width:45px;
            height:45px;
            border:none;
            border-radius:12px;
            background:#111827;
            color:white;
            cursor:pointer;
            font-size:18px;
            transition:0.3s;
        }

        body.dark .theme-btn{
            background:#f9fafb;
            color:#111827;
        }

        .theme-btn:hover{
            transform:scale(1.05);
        }

        .logout-btn{
            background:#ef4444;
            color:white;
            padding:12px 18px;
            border:none;
            border-radius:12px;
            cursor:pointer;
            font-size:14px;
            transition:0.3s;
        }

        .logout-btn:hover{
            background:#dc2626;
        }

        /* Card */

        .card{
            background:white;
            padding:25px;
            border-radius:18px;
            box-shadow:0 5px 20px rgba(0,0,0,0.08);
            transition:0.3s;
        }

        body.dark .card{
            background:#1f2937;
            color:white;
        }

        @media(max-width:900px){

            .sidebar{
                width:220px;
            }

            .main{
                margin-left:220px;
                width:calc(100% - 220px);
            }

        }

    </style>

</head>

<body>

    <!-- Sidebar -->

    <div class="sidebar">

        <div class="logo">

            Social Manager

        </div>

        <div class="menu">

            <a href="{{ route('dashboard') }}">
                Dashboard
            </a>

            <a href="#">
                Facebook Accounts
            </a>

            <a href="{{ route('posts.create') }}">
                Posts
            </a>

            <a href="#">
                Comments
            </a>

            <a href="#">
                Messages
            </a>

            <a href="#">
                Settings
            </a>

        </div>

    </div>

    <!-- Main -->

    <div class="main">

        <!-- Topbar -->

        <div class="topbar">

            <div class="welcome">

                <h2>
                    Welcome, {{ auth()->user()->name }}
                </h2>

                <p>
                    Manage your Facebook automation system
                </p>

            </div>

            <div class="top-actions">

                <!-- Theme -->

                <button class="theme-btn" onclick="toggleTheme()" id="themeButton">

                    ◐

                </button>

                <!-- Logout -->

                <form method="POST" action="{{ route('logout') }}">

                    @csrf

                    <button class="logout-btn">

                        Logout

                    </button>

                </form>

            </div>

        </div>

        <!-- Page Content -->

        @yield('content')

    </div>

    <script>

        function toggleTheme(){

            document.body.classList.toggle('dark');

            let themeButton = document.getElementById('themeButton');

            if(document.body.classList.contains('dark')){

                themeButton.innerHTML = '☼';

                localStorage.setItem('theme','dark');

            }else{

                themeButton.innerHTML = '◐';

                localStorage.setItem('theme','light');

            }

        }

        // Load Theme

        if(localStorage.getItem('theme') === 'dark'){

            document.body.classList.add('dark');

            document.getElementById('themeButton').innerHTML = '☼';

        }

    </script>

</body>

</html>