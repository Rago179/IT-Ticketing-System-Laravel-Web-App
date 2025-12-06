<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User Management</title>
    {{-- IMPORTANT: This loads Tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans p-5 bg-gray-50 text-slate-800">

    {{-- Notifications --}}
    @if (session('success'))
        <div id="success-alert" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 bg-green-100 text-green-800 px-6 py-3 rounded-lg shadow-md font-bold flex items-center gap-3">
            <span>{{ session('success') }}</span>
        </div>
        <script>setTimeout(() => document.getElementById('success-alert')?.remove(), 3000);</script>
    @endif
    @if (session('error'))
        <div id="error-alert" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 bg-red-100 text-red-800 px-6 py-3 rounded-lg shadow-md font-bold border border-red-500 flex items-center gap-3">
            <span>🚫 {{ session('error') }}</span>
        </div>
        <script>setTimeout(() => document.getElementById('error-alert')?.remove(), 4000);</script>
    @endif

    <div class="max-w-5xl mx-auto bg-white p-8 rounded-lg shadow-sm">
        <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-4">
            <h1 class="text-2xl font-bold text-slate-800 m-0">User Management</h1>
            <a href="{{ route('home') }}" class="text-sky-600 font-bold no-underline hover:underline">&larr; Back to Dashboard</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse mt-2 text-sm">
                <thead>
                    <tr>
                        <th class="p-3 text-left border-b border-gray-100 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">ID</th>
                        <th class="p-3 text-left border-b border-gray-100 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">Name</th>
                        <th class="p-3 text-left border-b border-gray-100 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">Email</th>
                        <th class="p-3 text-left border-b border-gray-100 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">Role</th>
                        <th class="p-3 text-left border-b border-gray-100 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">Status</th>
                        <th class="p-3 text-left border-b border-gray-100 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-50 border-b border-gray-50 last:border-none transition-colors">
                            <td class="p-3 text-slate-500">{{ $user->id }}</td>
                            <td class="p-3">
                                <a href="{{ route('users.show', $user) }}" class="font-bold text-sky-600 no-underline hover:underline">
                                    {{ $user->name }}
                                </a>
                            </td>
                            <td class="p-3 text-slate-600">{{ $user->email }}</td>
                            <td class="p-3">
                                {{-- Role Change Dropdown --}}
                                @if($user->id !== Auth::id())
                                    <form action="{{ route('users.updateRole', $user) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" 
                                            onchange="this.form.submit()" 
                                            class="py-1.5 pl-3 pr-8 border border-gray-300 rounded text-sm focus:ring-sky-500 focus:border-sky-500 bg-white cursor-pointer text-slate-700 shadow-sm leading-tight">
                                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                            <option value="it" {{ $user->role === 'it' ? 'selected' : '' }}>IT</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </form>
                                @else
                                    <span class="px-4 py-1.5 inline-block whitespace-nowrap rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">
                                        Admin (You)
                                    </span>
                                @endif
                            </td>
                            <td class="p-3">
                                @if($user->is_blocked)
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">🚫 Blocked</span>
                                @else
                                    <span class="text-green-600 text-sm font-bold flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> Active
                                    </span>
                                @endif
                            </td>
                            <td class="p-3">
                                {{-- Block/Unblock Button --}}
                                @if($user->id !== Auth::id())
                                    <form action="{{ route('users.block', $user) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        @if($user->is_blocked)
                                            <button type="submit" class="px-3 py-1.5 rounded bg-green-600 text-white text-xs font-bold hover:bg-green-700 border-none cursor-pointer transition-colors shadow-sm" title="Unblock this user">Unblock</button>
                                        @else
                                            <button type="submit" class="px-3 py-1.5 rounded bg-red-600 text-white text-xs font-bold hover:bg-red-700 border-none cursor-pointer transition-colors shadow-sm" title="Block this user" onclick="return confirm('Are you sure you want to block this user?')">Block</button>
                                        @endif
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
</body>
</html>