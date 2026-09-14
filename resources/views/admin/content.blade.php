@extends('layouts.header')

@section('title', 'Content')

<div class="admin-wrap">
    <aside class="admin-sidebar">
        <div class="brand">Guilford Admin</div>
        <nav>
            <a href="{{ url('/admin') }}">Dashboard</a>
            <a href="{{ url('/admin/users') }}">Users</a>
            <a href="{{ url('/admin/content') }}" class="active">Content</a>
            <a href="{{ url('/admin/sections') }}">Sections</a>
            <a href="{{ url('/admin/subscribers') }}">Subscribers</a>
            <a href="{{ url('/moderator') }}">Moderation</a>
            <a href="{{ url('/') }}">← Site</a>
        </nav>
    </aside>

    <div class="admin-main">
        <h1>Content</h1>

        <form method="post" action="{{ route('admin.content.create') }}">
            @csrf
            <select name="section_id" required>
                @foreach($sections as $s)
                    <option value="{{ $s->id }}">{{ $s->title }}</option>
                @endforeach
            </select>
            <input name="title" placeholder="Title" required>
            <textarea name="body" placeholder="Body" rows="3"></textarea>
            <button class="btn-primary">Create</button>
        </form>

        <h2>All Content ({{ $items->count() }})</h2>
        <table class="data-table">
            <thead><tr><th>Title</th><th>Section</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($items as $c)
                    <tr>
                        <td><strong>{{ $c->title }}</strong></td>
                        <td>{{ $c->section->title ?? '—' }}</td>
                        <td>
                            <form method="post" action="{{ route('admin.content.delete', $c) }}" style="display:inline" onsubmit="return confirm('Delete?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-sm danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('layouts.footer')