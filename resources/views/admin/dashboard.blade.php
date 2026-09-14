@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="stat-grid">
    <div class="stat">
        <div class="stat-num">{{ $stats['users'] }}</div>
        <div class="stat-label">Total Users</div>
    </div>
    <div class="stat">
        <div class="stat-num">{{ $stats['moderators'] }}</div>
        <div class="stat-label">Moderators</div>
    </div>
    <div class="stat warn">
        <div class="stat-num">{{ $stats['pending'] }}</div>
        <div class="stat-label">Pending Review</div>
    </div>
    <div class="stat">
        <div class="stat-num">{{ $stats['content'] }}</div>
        <div class="stat-label">Published Items</div>
    </div>
    <div class="stat">
        <div class="stat-num">{{ $stats['subscribers'] }}</div>
        <div class="stat-label">Subscribers</div>
    </div>
</div>

<h2>Recent Content</h2>
<table class="data-table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Section</th>
            <th>Updated</th>
        </tr>
    </thead>
    <tbody>
        @forelse($recentContent as $c)
            <tr>
                <td><strong>{{ $c->title }}</strong></td>
                <td><span class="tag">{{ $c->section->title ?? '—' }}</span></td>
                <td>{{ $c->updated_at->diffForHumans() }}</td>
            </tr>
        @empty
            <tr><td colspan="3" style="text-align:center;padding:30px;color:#6B6B6B">No content yet.</td></tr>
        @endforelse
    </tbody>
</table>

@endsection