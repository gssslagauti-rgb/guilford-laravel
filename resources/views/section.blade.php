@extends('layouts.header')

@section('title', $section->title)

<section class="hero">
    <div class="container">
        <h1>{{ $section->title }}</h1>
        <p class="hero-subtitle">{{ $section->description }}</p>
    </div>
</section>

<div class="container">
    <section class="section">
        <div class="section-header">
            <h2>{{ $items->count() }} items</h2>
        </div>

        @if($items->isEmpty())
            <p>No content yet.</p>
        @else
            <div class="card-grid">
                @foreach($items as $item)
                    <div class="card">
                        <h3>{{ $item->title }}</h3>
                        @if($item->body)
                            <p>{{ $item->body }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>

@include('layouts.footer')