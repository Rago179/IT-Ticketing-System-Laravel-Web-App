<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Simple App</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .nav { margin-bottom: 20px; padding: 10px; background: #eee; }
    </style>
</head>
<body>
    <div class="nav">
        @auth
            <span>Welcome back, {{ Auth::user()->name }}!</span> | 
            <a href="{{ url('/dashboard') }}">Dashboard</a>
        @else
            <a href="{{ route('login') }}">Login</a> | 
            <a href="{{ route('register') }}">Register</a>
        @endauth
    </div>

    <h1>Hello World</h1>
    <p>This is my own simple HTML page, free from Tailwind and complex layouts.</p>

    @if(isset($posts))
        <h2>Recent Posts:</h2>
        <ul>
            @foreach($posts as $post)
                <li>{{ $post->title }}</li>
            @endforeach
        </ul>
    @endif
</body>
</html> -->