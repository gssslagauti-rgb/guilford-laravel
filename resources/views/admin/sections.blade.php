@extends('layouts.admin')

@section('title', 'Sections')
@section('page-title', 'Sections')

@section('content')

<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Slug</th>
            <th>Title</th>
            <th>Description</th>
            <th>Type</th>
            <th>Order</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sections as $s)
            <tr>
                <td>{{ $s->id }}</td>
                <td><code>{{ $s->slug }}</code></td>
                <td><strong>{{ $s->title }}</strong></td>
                <td>{{ $s->description ?? '—' }}</td>
                <td>
                    @if($s->is_meeting)
                        <span class="tag warn">Meeting</span>
                    @else
                        <span class="tag">Standard</span>
                    @endif
                </td>
                <td>{{ $s->order_index }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection