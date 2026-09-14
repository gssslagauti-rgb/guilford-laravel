@extends('layouts.moderator')

@section('title', 'Moderator')
@section('page-title', 'Pending Contributions')

@section('content')

@if($pending->isEmpty())
    <p style="text-align:center;color:#6B6B6B;padding:60px;background:#fff;border-radius:12px;border:1px solid #E8E4DA">
        All caught up! No pending contributions. 🎉
    </p>
@else
    @foreach($pending as $p)
        <div class="mod-card">
            <div class="mod-head">
                <span class="tag">{{ $p->section->title ?? $p->section->slug }}</span>
                <span class="tag warn">{{ $p->contribution_type }}</span>
                <span>by <strong>{{ $p->user->display_name ?? 'Anonymous' }}</strong> · {{ $p->created_at->diffForHumans() }}</span>
            </div>

            <pre>{{ json_encode($p->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

            <form method="post" action="{{ route('moderator.moderate', $p) }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                @csrf
                <input name="note" placeholder="Optional moderator note" style="flex:1;min-width:200px;padding:10px 14px;border:1.5px solid #D8D4CA;border-radius:8px;font-family:inherit">
                <button name="action" value="approved" class="btn-sm ok">✓ Approve</button>
                <button name="action" value="rejected" class="btn-sm danger">✕ Reject</button>
            </form>
        </div>
    @endforeach
@endif

@endsection