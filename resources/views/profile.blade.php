<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - {{ config('app.name', 'IT-Ticket-System') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans p-5 bg-gray-50 text-slate-800">

    <div class="max-w-4xl mx-auto">
        
        {{-- Custom Header (Consistent with other pages) --}}
        <div class="bg-white p-6 rounded-lg shadow-sm mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-slate-800 m-0">Profile</h1>
            <div class="flex items-center gap-4 text-sm font-bold text-slate-700">
                <span>
                    Hi, <span class="text-sky-600">{{ Auth::user()->name }}</span>
                </span>
                <span class="text-slate-300">|</span>
                <a href="{{ route('home') }}" class="text-slate-500 hover:text-sky-600 no-underline transition-colors">
                    Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline ml-2">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700 underline bg-transparent border-none cursor-pointer">
                        Log Out
                    </button>
                </form>
            </div>
        </div>

        <a href="{{ route('home') }}" class="inline-block mb-6 text-sky-600 font-bold hover:underline">
            &larr; Back to Dashboard
        </a>

        {{-- Profile Forms Container --}}
        <div class="space-y-6">
            
            {{-- Update Profile Information --}}
            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg border border-gray-100">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-slate-900 mb-4">Profile Information</h2>
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            {{-- Update Password --}}
            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg border border-gray-100">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-slate-900 mb-4">Update Password</h2>
                    <livewire:profile.update-password-form />
                </div>
            </div>

            {{-- Delete User --}}
            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg border border-gray-100">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-red-600 mb-4">Delete Account</h2>
                    <livewire:profile.delete-user-form />
                </div>
            </div>

        </div>
    </div>

</body>
</html>