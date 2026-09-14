@extends('layouts.header')

@section('title', 'Moderator')

<div class="admin-wrap">
    <aside class="admin-sidebar">
        <div class="brand">Moderator</div>
        <nav>
            <a href="{{ url('/moderator') }}" class="active">Pending</a>
            @if(auth()->user()->isAdmin())
                <a href="{{ url('/admin') }}">Admin</a>
            @endif
            <a href="{{ url('/') }}">← Site</a>
        </nav>
    </aside>

    <div class="admin-main">
        <h1>Pending ({{ $pending->count() }})</h1>

        @if($pending->isEmpty())
            <p>All caught up.</p>
        @else
            @foreach($pending as $p)
                <div class="mod-card">
                    <div class="mod-head">
                        <span class="tag">{{ $p->section->slug }}</span>
                    </div>
                    <pre>{{ json_encode($p->content, JSON_PRETTY_PRINT) }}</pre>
                    <form method="post" action="{{ route('moderator.moderate', $p) }}">
                        @csrf
                        <input name="note" placeholder="Note">
                        <button name="action" value="approved" class="btn-sm ok">Approve</button>
                        <button name="action" value="rejected" class="btn-sm danger">Reject</button>
                    </form>
                </div>
            @endforeach
        @endif
    </div>
</div>

@include('layouts.footer')