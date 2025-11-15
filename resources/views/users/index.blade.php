<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User Management</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #333; }
        .back-btn { text-decoration: none; color: #3490dc; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8fafc; color: #64748b; font-weight: bold; }
        tr:hover { background-color: #f1f5f9; }

        /* Badges */
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 0.85em; font-weight: bold; text-transform: capitalize; }
        .badge-admin { background-color: #e0e7ff; color: #3730a3; }
        .badge-it { background-color: #dcfce7; color: #166534; }
        .badge-user { background-color: #f1f5f9; color: #475569; }
        .badge-blocked { background-color: #fee2e2; color: #dc2626; margin-left: 5px; }

        /* Forms & Buttons */
        select { padding: 5px; border-radius: 4px; border: 1px solid #ccc; }
        .action-btn { padding: 5px 10px; border-radius: 4px; border: none; cursor: pointer; font-size: 0.9em; color: white; }
        .block-btn { background-color: #dc2626; }
        .unblock-btn { background-color: #16a34a; }
        
        .pagination-wrapper { margin-top: 20px; }
    </style>
</head>
<body>
    {{-- Notifications --}}
    @if (session('success'))
        <div style="background-color: #dcfce7; color: #166534; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div style="background-color: #fee2e2; color: #dc2626; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            🚫 {{ session('error') }}
        </div>
    @endif

    <div class="container">
        <div class="header">
            <h1>User Management</h1>
            <a href="{{ route('home') }}" class="back-btn">&larr; Back to Dashboard</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <a href="{{ route('users.show', $user) }}" style="text-decoration: none; color: #3490dc; font-weight: bold;">
                                {{ $user->name }}
                            </a>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            {{-- Role Change Dropdown --}}
                            @if($user->id !== Auth::id())
                                <form action="{{ route('users.updateRole', $user) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" onchange="this.form.submit()">
                                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                        <option value="it" {{ $user->role === 'it' ? 'selected' : '' }}>IT</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </form>
                            @else
                                <span class="badge badge-admin">Admin (You)</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_blocked)
                                <span class="badge badge-blocked">🚫 Blocked</span>
                            @else
                                <span style="color: #16a34a; font-size: 0.9em;">Active</span>
                            @endif
                        </td>
                        <td>
                            {{-- Block/Unblock Button --}}
                            @if($user->id !== Auth::id())
                                <form action="{{ route('users.block', $user) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    @if($user->is_blocked)
                                        <button type="submit" class="action-btn unblock-btn" title="Unblock this user">Unblock</button>
                                    @else
                                        <button type="submit" class="action-btn block-btn" title="Block this user" onclick="return confirm('Are you sure you want to block this user?')">Block</button>
                                    @endif
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $users->links() }}
        </div>
    </div>
</body>
</html>