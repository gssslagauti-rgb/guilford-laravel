@extends('layouts.header')

@section('title', $section->title)

<section class="hero">
    <div class="hero-bg"></div>
    <div class="container hero-content">
        <span class="hero-eyebrow">Section</span>
        <h1>{{ $section->title }}</h1>
        @if($section->description)
            <p class="hero-subtitle">{{ $section->description }}</p>
        @endif
    </div>
</section>

<div class="container main-content">

    <section class="stats-bar">
        <div class="stats-bar-inner">
            <div class="stats-item">
                <span class="stats-num">{{ $items->count() }}</span>
                <span class="stats-label">Listings</span>
            </div>
            <div class="stats-item">
                <span class="stats-num">{{ $section->is_meeting ? 'Yes' : 'No' }}</span>
                <span class="stats-label">Meeting Section</span>
            </div>
            <div class="stats-item">
                <span class="stats-num">{{ $section->order_index }}</span>
                <span class="stats-label">Display Order</span>
            </div>
            <div class="stats-item">
                <span class="stats-num">{{ $section->slug }}</span>
                <span class="stats-label">Slug</span>
            </div>
        </div>
    </section>

    @if($items->isEmpty())
        <section class="section">
            <p style="text-align:center;color:#6B6B6B;padding:60px 0;font-style:italic">
                No content in this section yet.
            </p>
        </section>
    @elseif($section->is_meeting)
        @php $m = $items->first()->metadata; @endphp
        <section class="section">
            <div class="section-header">
                <div class="section-title-group">
                    <h2>Most Recent Meeting</h2>
                    @if(!empty($m['date']))
                        <p class="section-desc">{{ $m['date'] }}@if(!empty($m['location'])) · {{ $m['location'] }}@endif</p>
                    @endif
                </div>
            </div>
            <div class="meeting-card">
                <ul class="meeting-list">
                    @foreach(($m['bullets'] ?? []) as $bullet)
                        <li>{{ $bullet }}</li>
                    @endforeach
                </ul>
            </div>
        </section>
    @else
        <section class="section">
            <div class="section-header">
                <div class="section-title-group">
                    <h2>All Listings ({{ $items->count() }})</h2>
                </div>
            </div>

            <div class="card-grid">
                @foreach($items as $item)
                    @php $m = $item->metadata; @endphp
                    <article class="card">
                        @if(!empty($m['category']))
                            <span class="card-tag">{{ $m['category'] }}</span>
                        @elseif($section->slug === 'events')
                            <span class="card-tag tag-blue">Event</span>
                        @elseif($section->slug === 'sports')
                            <span class="card-tag tag-green">Sports</span>
                        @elseif($section->slug === 'recommendations')
                            <span class="card-tag tag-amber">Recommended</span>
                        @else
                            <span class="card-tag tag-navy">{{ ucfirst($section->slug) }}</span>
                        @endif

                        <h3>{{ $item->title }}</h3>

                        @if(!empty($m['date']) || !empty($m['location']))
                            <div class="card-meta">
                                @if(!empty($m['date']))
                                    <span class="meta-icon">📅</span> {{ $m['date'] }}
                                @endif
                                @if(!empty($m['location']))
                                    <span class="meta-sep">·</span>
                                    <span class="meta-icon">📍</span> {{ $m['location'] }}
                                @endif
                            </div>
                        @endif

                        @if(!empty($m['season']))
                            <div class="card-meta"><span class="meta-icon">🏆</span> Season: {{ $m['season'] }}</div>
                        @endif
                        @if(!empty($m['registration']))
                            <div class="card-meta"><span class="meta-icon">📝</span> Registration: {{ $m['registration'] }}</div>
                        @endif

                        @if($item->body)
                            <p class="card-body">{{ $item->body }}</p>
                        @endif

                        <div class="card-footer">
                            @if($item->contributor_name)
                                <span class="card-author">Added by {{ $item->contributor_name }}</span>
                            @else
                                <span></span>
                            @endif
                            <span class="card-arrow">→</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

</div>

@include('layouts.footer')