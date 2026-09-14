@extends('layouts.admin')

@section('title', 'Facebook Posts')
@section('page-title', 'Facebook Aggregation')

@section('content')

<div class="fb-warning">
    <strong>Note:</strong> Facebook's terms prevent automated scraping. Copy the text of each post from the source page and paste it below.
    Click <em>Summarize with Claude</em> to clean it up automatically (requires API credits).
</div>

<h2>Add Facebook Post</h2>

<form method="post" action="{{ route('admin.facebook.store') }}" id="fb-form">
    @csrf

    <label>Source Page</label>
    <select name="source_page" required>
        <option value="">— choose a page —</option>
        @foreach($sources as $slug => $label)
            <option value="{{ $slug }}">{{ $label }}</option>
        @endforeach
    </select>

    <label>Original Post Text</label>
    <textarea name="original_text" id="fb-original" rows="5" placeholder="Paste the Facebook post text here..." required></textarea>

    <label>Summary (optional)</label>
    <textarea name="summary" id="fb-summary" rows="3" placeholder="Rewrite or click Summarize with Claude below..."></textarea>

    <button type="button" class="btn-claude" id="btn-summarize">✨ Summarize with Claude</button>
    <span id="summarize-status"></span>

    <div class="fb-row">
        <div>
            <label>Post Date</label>
            <input type="date" name="post_date">
        </div>
        <div>
            <label>Post URL (optional)</label>
            <input type="url" name="post_url" placeholder="https://facebook.com/...">
        </div>
    </div>

    <label class="fb-check">
        <input type="checkbox" name="published" value="1"> Publish immediately
    </label>

    <button type="submit" class="btn-primary">Save Post</button>
</form>

<h2>All Facebook Posts ({{ $posts->count() }})</h2>

@if($posts->isEmpty())
    <p style="text-align:center;color:#6B6B6B;padding:40px;background:#fff;border-radius:12px;border:1px solid #E8E4DA">
        No Facebook posts imported yet.
    </p>
@else
    <table class="data-table">
        <thead>
            <tr>
                <th>Source</th>
                <th>Summary / Original</th>
                <th>Date</th>
                <th>Status</th>
                <th>Added By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $p)
                <tr>
                    <td><span class="tag">{{ $p->source_page }}</span></td>
                    <td>
                        @if($p->summary)
                            <strong>{{ \Illuminate\Support\Str::limit($p->summary, 120) }}</strong>
                        @else
                            <em style="color:#999">{{ \Illuminate\Support\Str::limit($p->original_text, 120) }}</em>
                        @endif
                    </td>
                    <td>{{ $p->post_date ? $p->post_date->format('M j, Y') : '—' }}</td>
                    <td>
                        @if($p->published)
                            <span class="tag ok">Published</span>
                        @else
                            <span class="tag warn">Draft</span>
                        @endif
                    </td>
                    <td>{{ $p->added_by ?? '—' }}</td>
                    <td>
                        <form method="post" action="{{ route('admin.facebook.destroy', $p) }}" onsubmit="return confirm('Delete this post?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn-sm danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<script>
document.getElementById('btn-summarize').addEventListener('click', async function () {
    var text = document.getElementById('fb-original').value.trim();
    var source = document.querySelector('select[name="source_page"]').value;
    var status = document.getElementById('summarize-status');
    var summary = document.getElementById('fb-summary');

    if (text.length < 20) { status.textContent = 'Paste a longer post first.'; return; }
    if (!source) { status.textContent = 'Choose a source page first.'; return; }

    status.textContent = 'Asking Claude...';

    try {
        var res = await fetch('{{ route('admin.facebook.summarize') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ text: text, source: source })
        });
        var data = await res.json();
        if (data.summary) {
            summary.value = data.summary;
            status.textContent = 'Done.';
        } else {
            status.textContent = 'Error: ' + (data.error || 'unknown');
        }
    } catch (e) {
        status.textContent = 'Request failed.';
    }
});
</script>

@endsection