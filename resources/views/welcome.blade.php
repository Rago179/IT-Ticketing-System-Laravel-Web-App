<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - {{ config('app.name', 'IT Ticket System') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans flex justify-center items-center min-h-screen bg-gray-100 p-5 box-border">

    <div class="bg-white p-10 rounded-lg shadow-md w-full max-w-[600px] text-center">
        <h1 class="text-sky-600 mt-0 text-4xl font-bold mb-4">IT-Ticket-System</h1>
        
        <p class="text-gray-600 leading-relaxed mb-8 text-lg">
            Welcome to our internal support platform. This application allows users to submit, track, and manage IT support tickets efficiently. Get help with your technical issues or manage requests all in one place.
        </p>

        <div class="text-left my-8 bg-slate-50 p-6 rounded-lg border-l-4 border-sky-600">
            <h3 class="text-gray-800 mt-0 mb-4 font-bold text-lg">Who is this for?</h3>
            <ul class="pl-5 list-disc m-0">
                <li class="mb-2 leading-snug text-gray-700">
                    <strong class="text-sky-700">Standard Users:</strong> Create new support tickets, view your ticket history, and communicate with support staff via comments.
                </li>
                <li class="mb-2 leading-snug text-gray-700">
                    <strong class="text-sky-700">IT Staff:</strong> View all incoming tickets, assign them to yourself, update statuses, and resolve technical issues.
                </li>
                <li class="mb-2 leading-snug text-gray-700">
                    <strong class="text-sky-700">Administrators:</strong> Full system oversight with capabilities to manage users, categories, and advanced settings.
                </li>
            </ul>
        </div>

        <div class="flex justify-center gap-5 mt-10">
            @auth
                <a href="{{ route('home') }}" class="inline-block px-8 py-3 rounded-md font-bold text-base bg-green-600 text-white hover:bg-green-700 transition-colors no-underline shadow-sm">
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-block px-8 py-3 rounded-md font-bold text-base bg-sky-600 text-white hover:bg-sky-700 transition-colors no-underline shadow-sm">
                    Log In
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-block px-8 py-3 rounded-md font-bold text-base bg-slate-200 text-slate-800 hover:bg-slate-300 transition-colors no-underline shadow-sm">
                        Register
                    </a>
                @endif
            @endauth
        </div>
    </div>

</body>
</html>