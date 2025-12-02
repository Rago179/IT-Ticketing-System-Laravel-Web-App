<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    {{-- IMPORTANT: This loads Tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans flex justify-center items-center min-h-screen bg-gray-100 p-5">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-sm">
        <h2 class="text-center text-2xl font-bold mb-6 text-slate-800">Reset Password</h2>

        <div class="text-sm text-slate-600 mb-6 leading-relaxed">
            Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
        </div>

        @if (session('status'))
            <div class="text-green-700 bg-green-100 p-3 rounded-md mb-4 text-sm font-bold border border-green-200">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-5">
                <label for="email" class="block mb-1 font-bold text-slate-700">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full p-2.5 border border-gray-300 rounded-md focus:ring-sky-500 focus:border-sky-500 shadow-sm">
                @error('email') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="w-full py-3 bg-sky-600 text-white rounded-md font-bold text-base hover:bg-sky-700 transition-colors cursor-pointer border-none">
                Email Password Reset Link
            </button>
        </form>

        <a href="{{ route('login') }}" class="block text-center mt-6 text-slate-500 hover:text-slate-700 hover:underline text-sm no-underline transition-colors">
            &larr; Back to Login
        </a>
    </div>

</body>
</html>