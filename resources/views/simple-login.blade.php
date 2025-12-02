<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    {{-- IMPORTANT: This loads Tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans flex justify-center items-center min-h-screen bg-gray-100 p-5">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-sm">
        <h2 class="text-center text-2xl font-bold mb-6 text-slate-800">Log In</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="mb-5">
                <label for="email" class="block mb-1 font-bold text-slate-700">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full p-2.5 border border-gray-300 rounded-md focus:ring-sky-500 focus:border-sky-500 shadow-sm">
                @error('email') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Password --}}
            <div class="mb-5">
                <label for="password" class="block mb-1 font-bold text-slate-700">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full p-2.5 border border-gray-300 rounded-md focus:ring-sky-500 focus:border-sky-500 shadow-sm">
                @error('password') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Remember Me & Forgot Password --}}
            <div class="flex justify-between items-center mb-6 text-sm">
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" 
                           class="rounded border-gray-300 text-sky-600 shadow-sm focus:ring-sky-500 mr-2">
                    <label for="remember" class="text-slate-600 cursor-pointer select-none">Remember me</label>
                </div>
                
                <a href="{{ route('password.request') }}" class="text-sky-600 hover:text-sky-800 hover:underline no-underline font-medium">
                    Forgot password?
                </a>
            </div>

            <button type="submit" class="w-full py-3 bg-sky-600 text-white rounded-md font-bold text-base hover:bg-sky-700 transition-colors cursor-pointer border-none shadow-sm">
                Log In
            </button>
        </form>

        <div class="text-center mt-6 text-sm text-slate-600">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-sky-600 hover:text-sky-800 hover:underline font-bold no-underline">
                Register
            </a>
        </div>
    </div>

</body>
</html>