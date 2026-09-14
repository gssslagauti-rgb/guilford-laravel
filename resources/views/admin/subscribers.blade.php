@extends('layouts.admin')

@section('title', 'Subscribers')
@section('page-title', 'Newsletter Subscribers')

@section('content')

<h2>Subscribers ({{ $subscribers->count() }})</h2>

@if($subscribers->isEmpty())
    <p style="text-align:center;color:#6B6B6B;padding:40px;background:#fff;border-radius:12px;border:1px solid #E8E4DA">
        No subscribers yet.
    </p>
@else
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Email</th>
                <th>Subscribed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subscribers as $i => $s)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $s->email }}</td>
                    <td>{{ $s->created_at ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@endsection