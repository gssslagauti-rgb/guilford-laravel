@extends('layouts.header')

@section('title', $section->title)

@section('content')
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
    <section class="section">
        <div class="section-header">
            <div class="section-title-group">
                <h2>{{ $items->count() }} {{ $items->count() === 1 ? 'item' : 'items' }}</h2>
                <p class="section-desc">Hover a card to edit · click + to add a new entry</p>
            </div>
            <div class="section-actions">
                <button type="button"
                        class="btn-add"
                        data-contribute="{{ $section->slug }}"
                        title="Add to {{ $section->title }}"
                        @guest onclick="openAuthModal(event)" @endguest>+</button>
            </div>
        </div>

        @if($items->isEmpty())
            <p style="text-align:center;color:#6B6B6B;padding:50px;font-style:italic">
                No content yet.
            </p>
        @elseif($section->is_meeting)
            @php $m = $items->first()->metadata; @endphp
            <div class="meeting-card">
                <ul class="meeting-list">
                    @foreach(($m['bullets'] ?? []) as $bullet)
                        <li>{{ $bullet }}</li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="card-grid">
                @foreach($items as $item)
                    @php
                        $m = $item->metadata;
                        $editPayload = json_encode([
                            'id' => $item->id,
                            'section' => $section->slug,
                            'title' => $item->title,
                            'body' => $item->body,
                            'date' => $m['date'] ?? '',
                            'location' => $m['location'] ?? '',
                            'season' => $m['season'] ?? '',
                            'registration' => $m['registration'] ?? '',
                            'category' => $m['category'] ?? '',
                            'bullets' => $m['bullets'] ?? [],
                        ]);
                    @endphp
                    <article class="card">
                        @if(!empty($m['category']))
                            <span class="card-tag">{{ $m['category'] }}</span>
                        @else
                            <span class="card-tag tag-navy">{{ ucfirst($section->slug) }}</span>
                        @endif

                        @auth
                            <button type="button"
                                    class="btn-edit"
                                    data-edit="{{ $editPayload }}"
                                    title="Edit this item">✎</button>
                      
                        @endauth

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

                        @if($item->body)
                            <p class="card-body">{{ \Illuminate\Support\Str::limit($item->body, 120) }}</p>
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
        @endif
    </section>
</div>
@endsection

