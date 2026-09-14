@extends('layouts.header')

@section('title', 'Guilford Hub')

@section('content')
<section class="hero">
    <div class="hero-bg"></div>
    <div class="container hero-content">
        <span class="hero-eyebrow">Est. 1639 · Long Island Sound</span>
        <h1>Welcome to Guilford</h1>
        <p class="hero-subtitle">One town, everything connected — events, government, sports, and local knowledge in one place.</p>
        <div class="hero-actions">
            <a href="{{ route('section', 'events') }}" class="btn-hero-primary">Explore Events</a>
            <a href="{{ route('section', 'recommendations') }}" class="btn-hero-ghost">Local Picks</a>
        </div>
    </div>
</section>

<div class="container main-content">
    <section class="stats-bar">
        <div class="stats-bar-inner">
            <div class="stats-item">
                <span class="stats-num">{{ collect($grouped)->sum(fn($g) => $g['items']->count()) }}</span>
                <span class="stats-label">Live Listings</span>
            </div>
            <div class="stats-item">
                <span class="stats-num">{{ count($grouped) }}</span>
                <span class="stats-label">Sections</span>
            </div>
            <div class="stats-item">
                <span class="stats-num">11</span>
                <span class="stats-label">Neighborhoods</span>
            </div>
            <div class="stats-item">
                <span class="stats-num">100%</span>
                <span class="stats-label">Community Made</span>
            </div>
        </div>
    </section>

    @foreach($grouped as $slug => $data)
        @if(in_array($slug, ['town-council', 'board-of-education']))
            @continue
        @endif

        @if($data['items']->isEmpty())
            @continue
        @endif

        <section class="section">
            <div class="section-header">
                <div class="section-title-group">
                    <h2>{{ $data['section']->title }}</h2>
                    @if($data['section']->description)
                        <p class="section-desc">{{ $data['section']->description }}</p>
                    @endif
                </div>

                <div class="section-actions">
                    <a href="{{ route('section', $slug) }}" class="btn-view-all">View all →</a>
                    <button type="button"
                            class="btn-add"
                            data-contribute="{{ $slug }}"
                            title="Add to {{ $data['section']->title }}"
                            @guest onclick="openAuthModal(event)" @endguest>+</button>
                </div>
            </div>

            <div class="card-grid">
                @foreach($data['items']->take(6) as $item)
                    @php
                        $m = $item->metadata;
                        $editPayload = json_encode([
                            'id' => $item->id,
                            'section' => $slug,
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
                        @elseif($slug === 'events')
                            <span class="card-tag tag-blue">Event</span>
                        @elseif($slug === 'sports')
                            <span class="card-tag tag-green">Sports</span>
                        @elseif($slug === 'recommendations')
                            <span class="card-tag tag-amber">Recommended</span>
                        @else
                            <span class="card-tag tag-navy">{{ ucfirst($slug) }}</span>
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
        </section>
    @endforeach

    @foreach(['town-council', 'board-of-education'] as $slug)
        @php
            $sec = \App\Models\Section::where('slug', $slug)->first();
            $items = $sec ? \App\Models\Content::where('section_id', $sec->id)->where('published', true)->orderByDesc('updated_at')->limit(1)->get() : collect();
        @endphp

        @if($items->isNotEmpty())
            @php
                $m = $items->first()->metadata;
                $editPayload = json_encode([
                    'id' => $items->first()->id,
                    'section' => $slug,
                    'title' => $items->first()->title,
                    'body' => $items->first()->body,
                    'date' => $m['date'] ?? '',
                    'location' => $m['location'] ?? '',
                    'bullets' => $m['bullets'] ?? [],
                ]);
            @endphp
            <section class="section">
                <div class="section-header">
                    <div class="section-title-group">
                        <h2>Most Recent {{ $sec->title }}</h2>
                        @if(!empty($m['date']))
                            <p class="section-desc">{{ $m['date'] }}@if(!empty($m['location'])) · {{ $m['location'] }}@endif</p>
                        @endif
                    </div>
                    <div class="section-actions">
                        @auth
                            <button type="button" class="btn-edit" data-edit="{{ $editPayload }}" title="Edit meeting notes">✎</button>
                         @endauth
                        <button type="button"
                                class="btn-add"
                                data-contribute="{{ $slug }}"
                                title="Add meeting notes"
                                @guest onclick="openAuthModal(event)" @endguest>+</button>
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
        @endif
    @endforeach
</div>
@endsection

