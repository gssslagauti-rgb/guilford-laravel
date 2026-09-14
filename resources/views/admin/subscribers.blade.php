@extends('layouts.header')

@section('title', 'Subscribers')

<div class="admin-wrap">
    <aside class="admin-sidebar">
        <div class="brand">Guilford Admin</div>
        <nav>
            <a href="{{ url('/admin') }}">Dashboard</a>
            <a href="{{ url('/admin/users') }}">Users</a>
            <a href="{{ url('/admin/content') }}">Content</a>
            <a href="{{ url('/admin/sections') }}">Sections</a>
            <a href="{{ url('/admin/subscribers') }}" class="active">Subscribers</a>
            <a href="{{ url('/moderator') }}">Moderation</a>
            <a href="{{ url('/') }}">← Site</a>
        </nav>
    </aside>

    <div class="admin-main">
        <h1>Subscribers ({{ $subscribers->count() }})</h1>
        <table class="data-table">
            <thead><tr><th>#</th><th>Email</th></tr></thead>
            <tbody>
                @foreach($subscribers as $i => $s)
                    <tr><td>{{ $i+1 }}</td><td>{{ $s->email }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('layouts.footer')