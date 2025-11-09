<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name', 'My App') }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; display: grid; place-items: center; min-height: 90vh; background-color: #f4f5f7; }
        .login-card { background-color: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); width: 100%; max-width: 400px; }
        .login-card h1 { text-align: center; margin-top: 0; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; /* Fixes padding issue */ }
        .error { color: #e53e3e; font-size: 0.875rem; margin-top: 0.25rem; }
        .submit-btn { width: 100%; padding: 0.75rem; border: none; border-radius: 4px; background-color: #3490dc; color: white; font-size: 1rem; font-weight: 600; cursor: pointer; }
        .submit-btn:hover { background-color: #2779bd; }
        .register-link { text-align: center; margin-top: 1rem; font-size: 0.9rem; }
        .register-link a { color: #3490dc; text-decoration: none; }
    </style>
</head>
<body>

    <div class="login-card">
        <h1>Login</h1>

        <!-- Session Status (e.g., for password resets) -->
        @if (session('status'))
            <p style="color: green; text-align: center;">{{ session('status') }}</p>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="submit-btn">
                Log in
            </button>
        </form>

        <div class="register-link">
            <p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
        </div>
    </div>

</body>
</html>