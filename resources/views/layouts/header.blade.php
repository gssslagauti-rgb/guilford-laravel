<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Guilford Hub') — Guilford Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="{{ url('/') }}" class="logo">
            <span class="logo-mark">G</span>
            <span class="logo-text">Guilford Hub</span>
        </a>

        <nav class="main-nav">
            <a href="{{ url('/') }}">Home</a>
            @foreach(\App\Models\Section::orderBy('order_index')->take(6)->get() as $nav)
                <a href="{{ route('section', $nav->slug) }}">{{ $nav->title }}</a>
            @endforeach
        </nav>

        <div class="auth-controls">
            @auth
                <span class="user-badge">👤 {{ auth()->user()->display_name }}</span>

                @if(auth()->user()->isModerator())
                    <a href="{{ url('/moderator') }}" class="btn-ghost">Moderate</a>
                @endif

                @if(auth()->user()->isAdmin())
                    <a href="{{ url('/admin') }}" class="btn-ghost">Admin</a>
                @endif

                <form method="post" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button class="btn-ghost">Log out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-ghost">Sign In</a>
                <a href="{{ route('register') }}" class="btn-primary">Join</a>
            @endauth
        </div>
    </div>
</header>

@if(session('success'))
    <div class="flash">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="flash err">{{ $errors->first() }}</div>
@endif

<main>