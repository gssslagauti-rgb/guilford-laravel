<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Guilford Hub Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div class="admin-wrap">
    <aside class="admin-sidebar">
        <div class="brand">Guilford Admin</div>
        <nav>
            <a href="{{ url('/admin') }}" class="{{ Request::is('admin') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ url('/admin/users') }}" class="{{ Request::is('admin/users*') ? 'active' : '' }}">Users</a>
            <a href="{{ url('/admin/content') }}" class="{{ Request::is('admin/content*') ? 'active' : '' }}">Content</a>
            <a href="{{ url('/admin/sections') }}" class="{{ Request::is('admin/sections*') ? 'active' : '' }}">Sections</a>
            <a href="{{ url('/admin/subscribers') }}" class="{{ Request::is('admin/subscribers*') ? 'active' : '' }}">Subscribers</a>
	    <a href="{{ url('/admin/facebook') }}" class="{{ Request::is('admin/facebook*') ? 'active' : '' }}">Facebook</a>
            <a href="{{ url('/moderator') }}">Moderation</a>
            <a href="{{ url('/') }}">← Back to Site</a>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:none;border:none;color:#C8D8E0;padding:11px 22px;font-size:.86rem;text-align:left;width:100%;cursor:pointer;font-family:inherit;border-left:3px solid transparent">
                    Log Out
                </button>
            </form>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-topbar">
            <h1>@yield('page-title', 'Dashboard')</h1>
            <div class="admin-user">👤 {{ auth()->user()->display_name }} · {{ auth()->user()->role }}</div>
        </div>

        @if(session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="flash err">{{ $errors->first() }}</div>
        @endif

        @yield('content')
    </main>
</div>

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>