<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - {{ config('app.name', 'IT Ticket System') }}</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f4f4f4; margin: 0; padding: 20px; box-sizing: border-box; }
        .container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 600px; text-align: center; }
        h1 { color: #3490dc; margin-top: 0; font-size: 2.5em; }
        p.description { color: #555; line-height: 1.6; margin-bottom: 30px; font-size: 1.1em; }
        .user-types { text-align: left; margin: 30px 0; background: #f8fafc; padding: 20px; border-radius: 8px; border-left: 4px solid #3490dc; }
        .user-types h3 { color: #333; margin-top: 0; margin-bottom: 15px; }
        .user-types ul { padding-left: 20px; margin: 0; }
        .user-types li { margin-bottom: 10px; line-height: 1.4; color: #444; }
        .user-types strong { color: #2779bd; }
        .auth-links { display: flex; justify-content: center; gap: 20px; margin-top: 40px; }
        .btn { display: inline-block; padding: 12px 30px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 16px; transition: background-color 0.2s; }
        .btn-login { background: #3490dc; color: white; }
        .btn-login:hover { background: #2779bd; }
        .btn-register { background: #e2e8f0; color: #333; }
        .btn-register:hover { background: #cbd5e0; }
        .btn-dashboard { background: #16a34a; color: white; }
        .btn-dashboard:hover { background: #15803d; }
    </style>
</head>
<body>
    <div class="container">
        <h1>IT-Ticket-System</h1>
        
        <p class="description">
            Welcome to our internal support platform. This application allows users to submit, track, and manage IT support tickets efficiently. Get help with your technical issues or manage requests all in one place.
        </p>

        <div class="user-types">
            <h3>Who is this for?</h3>
            <ul>
                <li><strong>Standard Users:</strong> Create new support tickets, view your ticket history, and communicate with support staff via comments.</li>
                <li><strong>IT Staff:</strong> View all incoming tickets, assign them to yourself, update statuses, and resolve technical issues.</li>
                <li><strong>Administrators:</strong> Full system oversight with capabilities to manage users, categories, and advanced settings.</li>
            </ul>
        </div>

        <div class="auth-links">
            @auth
                <a href="{{ route('home') }}" class="btn btn-dashboard">Go to Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-login">Log In</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-register">Register</a>
                @endif
            @endauth
        </div>
    </div>
</body>
</html>