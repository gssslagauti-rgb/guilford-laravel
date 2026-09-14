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
            <a href="{{ route('section', 'events') }}">Upcoming Events</a>
            <a href="{{ route('section', 'projects') }}">Ongoing Town Projects</a>
            <a href="{{ route('section', 'town-council') }}">Town Council Meeting</a>
            <a href="{{ route('section', 'board-of-education') }}">Board of Education Meeting</a>
            <a href="{{ route('section', 'sports') }}">Sports &amp; Registration</a>
            <a href="{{ route('section', 'construction') }}">Construction &amp; Permits</a>
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
    @yield('content')
</main>

{{-- Contribution modal — only shown to logged-in users --}}
@auth
<div id="contribution-modal" class="modal hidden">
    <div class="modal-content">
        <button type="button" class="modal-close" onclick="document.getElementById('contribution-modal').classList.add('hidden')">&times;</button>
        <h2 id="modal-title">Contribute</h2>
        <p class="modal-sub">Your submission will be reviewed by a moderator before appearing.</p>

        <form action="{{ route('contribute') }}" method="post" id="contribution-form">
            @csrf
            <input type="hidden" name="section" id="c-section">
            <input type="hidden" name="contribution_type" id="c-type" value="new">
            <input type="hidden" name="target_id" id="c-target">
            <div id="contribution-fields"></div>
            <button type="submit" class="btn-primary" id="modal-submit" style="width:100%;margin-top:8px">Submit for Review</button>
        </form>
    </div>
</div>
@endauth

{{-- Auth modal — shown to guests who click (+) or ✎ --}}
@guest
<div id="auth-modal" class="modal hidden">
    <div class="modal-content modal-narrow">
        <button type="button" class="modal-close" onclick="document.getElementById('auth-modal').classList.add('hidden')">&times;</button>
        <h2>Join Guilford Hub</h2>
        <p class="modal-sub">You need an account to contribute. It takes 30 seconds.</p>

        <div class="auth-tabs">
            <a href="{{ route('login') }}" class="auth-tab">Sign In</a>
            <a href="{{ route('register') }}" class="auth-tab active">Register</a>
        </div>

        <form method="post" action="{{ route('register') }}" class="auth-form">
            @csrf
            <label>Display Name</label>
            <input type="text" name="display_name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password (8+ characters)</label>
            <input type="password" name="password" required minlength="8">

            <button type="submit" class="btn-primary" style="width:100%">Create Account</button>
        </form>

        <p class="modal-foot">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
    </div>
</div>
@endguest

@include('layouts.footer')