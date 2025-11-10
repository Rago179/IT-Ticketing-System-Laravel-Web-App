<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f4f4f4; margin: 0; }
        .auth-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #3490dc; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #2779bd; }
        .error { color: #e3342f; font-size: 0.875em; margin-top: 5px; }
        .link { text-align: center; margin-top: 20px; font-size: 0.9em; }
        .link a { color: #3490dc; text-decoration: none; }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>Log In</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9em;">
                <div style="display: flex; align-items: center;">
                    <input type="checkbox" name="remember" id="remember" style="width: auto; margin-right: 8px;">
                    <label for="remember" style="margin-bottom: 0; font-weight: normal;">Remember me</label>
                </div>
                <a href="{{ route('password.request') }}" style="color: #3490dc; text-decoration: none;">Forgot password?</a>
            </div>
            <button type="submit">Log In</button>
        </form>

        <div class="link">
            Don't have an account? <a href="{{ route('register') }}">Register</a>
        </div>
    </div>
</body>
</html>