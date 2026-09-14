@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')

<h2>Create New User</h2>
<form method="post" action="{{ route('admin.users.create') }}" class="grid-2">
    @csrf
    <input name="display_name" placeholder="Display Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input name="password" placeholder="Password (6+ chars)" required minlength="6">
    <select name="role">
        <option value="user">User</option>
        <option value="moderator">Moderator</option>
        <option value="admin">Admin</option>
    </select>
    <button class="btn-primary" style="grid-column:1/-1">Create User</button>
</form>

<h2>All Users ({{ $users->count() }})</h2>
<table class="data-table">
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
        @foreach($users as $u)
            <tr>
                <td>{{ $u->id }}</td>
                <td><strong>{{ $u->display_name }}</strong></td>
                <td>{{ $u->email }}</td>
                <td>
                    @if($u->role === 'admin')
                        <span class="tag warn">Admin</span>
                    @elseif($u->role === 'moderator')
                        <span class="tag ok">Moderator</span>
                    @else
                        <span class="tag">User</span>
                    @endif
                </td>
                <td>
                    @if($u->is_banned)
                        <span class="tag warn">Banned</span>
                    @else
                        <span class="tag ok">Active</span>
                    @endif
                </td>
                <td>
                    @if($u->id !== auth()->id())
                        <form method="post" action="{{ route('admin.users.ban', $u) }}" style="display:inline">
                            @csrf
                            <button class="btn-sm">{{ $u->is_banned ? 'Unban' : 'Block' }}</button>
                        </form>
                        <form method="post" action="{{ route('admin.users.delete', $u) }}" style="display:inline" onsubmit="return confirm('Delete this user?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn-sm danger">Delete</button>
                        </form>
                    @else
                        <em style="color:#999">(you)</em>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection