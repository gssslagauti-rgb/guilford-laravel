@extends('layouts.admin')

@section('title', 'Content')
@section('page-title', 'Content Management')

@section('content')

<h2>Add New Content</h2>
<form method="post" action="{{ route('admin.content.create') }}">
    @csrf
    <select name="section_id" required>
        @foreach($sections as $s)
            <option value="{{ $s->id }}">{{ $s->title }}</option>
        @endforeach
    </select>
    <input name="title" placeholder="Title" required>
    <textarea name="body" placeholder="Body / description" rows="3"></textarea>
    <input name="date" placeholder="Date (e.g. Oct 12, 2026)">
    <input name="location" placeholder="Location">
    <input name="category" placeholder="Category">
    <button class="btn-primary">Create Content</button>
</form>

<h2>All Content ({{ $items->count() }})</h2>
<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Section</th>
            <th>Contributor</th>
            <th>Updated</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $c)
            <tr>
                <td>{{ $c->id }}</td>
                <td><strong>{{ $c->title }}</strong></td>
                <td><span class="tag">{{ $c->section->title ?? '—' }}</span></td>
                <td>{{ $c->contributor_name ?? '—' }}</td>
                <td>{{ $c->updated_at->diffForHumans() }}</td>
                <td>
                    <form method="post" action="{{ route('admin.content.delete', $c) }}" style="display:inline" onsubmit="return confirm('Delete this item?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn-sm danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection