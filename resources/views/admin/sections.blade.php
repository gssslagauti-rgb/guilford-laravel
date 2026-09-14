@extends('layouts.header')

@section('title', 'Sections')

<div class="admin-wrap">
    <aside class="admin-sidebar">
        <div class="brand">Guilford Admin</div>
        <nav>
            <a href="{{ url('/admin') }}">Dashboard</a>
            <a href="{{ url('/admin/users') }}">Users</a>
            <a href="{{ url('/admin/content') }}">Content</a>
            <a href="{{ url('/admin/sections') }}" class="active">Sections</a>
            <a href="{{ url('/admin/subscribers') }}">Subscribers</a>
            <a href="{{ url('/moderator') }}">Moderation</a>
            <a href="{{ url('/') }}">← Site</a>
        </nav>
    </aside>

    <div class="admin-main">
        <h1>Sections ({{ $sections->count() }})</h1>
        <table class="data-table">
            <thead><tr><th>Slug</th><th>Title</th><th>Type</th><th>Order</th></tr></thead>
            <tbody>
                @foreach($sections as $s)
                    <tr>
                        <td><code>{{ $s->slug }}</code></td>
                        <td><strong>{{ $s->title }}</strong></td>
                        <td>{{ $s->is_meeting ? 'Meeting' : 'Standard' }}</td>
                        <td>{{ $s->order_index }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('layouts.footer')