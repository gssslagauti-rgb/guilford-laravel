

<?php $__env->startSection('title', 'Guilford Hub'); ?>

<?php $__env->startSection('content'); ?>
<section class="hero">
    <div class="hero-bg"></div>
    <div class="container hero-content">
        <span class="hero-eyebrow">Est. 1639 · Long Island Sound</span>
        <h1>Welcome to Guilford</h1>
        <p class="hero-subtitle">One town, everything connected — events, government, sports, and local knowledge in one place.</p>
        <div class="hero-actions">
            <a href="<?php echo e(route('section', 'events')); ?>" class="btn-hero-primary">Explore Events</a>
            <a href="<?php echo e(route('section', 'recommendations')); ?>" class="btn-hero-ghost">Local Picks</a>
        </div>
    </div>
</section>

<div class="container main-content">
    <section class="stats-bar">
        <div class="stats-bar-inner">
            <div class="stats-item">
                <span class="stats-num"><?php echo e(collect($grouped)->sum(fn($g) => $g['items']->count())); ?></span>
                <span class="stats-label">Live Listings</span>
            </div>
            <div class="stats-item">
                <span class="stats-num"><?php echo e(count($grouped)); ?></span>
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

    <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(in_array($slug, ['town-council', 'board-of-education'])): ?>
            <?php continue; ?>
        <?php endif; ?>

        <?php if($data['items']->isEmpty()): ?>
            <?php continue; ?>
        <?php endif; ?>

        <section class="section">
            <div class="section-header">
                <div class="section-title-group">
                    <h2><?php echo e($data['section']->title); ?></h2>
                    <?php if($data['section']->description): ?>
                        <p class="section-desc"><?php echo e($data['section']->description); ?></p>
                    <?php endif; ?>
                </div>

                <div class="section-actions">
                    <a href="<?php echo e(route('section', $slug)); ?>" class="btn-view-all">View all →</a>
                    <button type="button"
                            class="btn-add"
                            data-contribute="<?php echo e($slug); ?>"
                            title="Add to <?php echo e($data['section']->title); ?>"
                            <?php if(auth()->guard()->guest()): ?> onclick="openAuthModal(event)" <?php endif; ?>>+</button>
                </div>
            </div>

            <div class="card-grid">
                <?php $__currentLoopData = $data['items']->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
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
                    ?>
                    <article class="card">
                        <?php if(!empty($m['category'])): ?>
                            <span class="card-tag"><?php echo e($m['category']); ?></span>
                        <?php elseif($slug === 'events'): ?>
                            <span class="card-tag tag-blue">Event</span>
                        <?php elseif($slug === 'sports'): ?>
                            <span class="card-tag tag-green">Sports</span>
                        <?php elseif($slug === 'recommendations'): ?>
                            <span class="card-tag tag-amber">Recommended</span>
                        <?php else: ?>
                            <span class="card-tag tag-navy"><?php echo e(ucfirst($slug)); ?></span>
                        <?php endif; ?>

                        <?php if(auth()->guard()->check()): ?>
                            <button type="button"
                                    class="btn-edit"
                                    data-edit="<?php echo e($editPayload); ?>"
                                    title="Edit this item">✎</button>
                        
                        <?php endif; ?>

                        <h3><?php echo e($item->title); ?></h3>

                        <?php if(!empty($m['date']) || !empty($m['location'])): ?>
                            <div class="card-meta">
                                <?php if(!empty($m['date'])): ?>
                                    <span class="meta-icon">📅</span> <?php echo e($m['date']); ?>

                                <?php endif; ?>
                                <?php if(!empty($m['location'])): ?>
                                    <span class="meta-sep">·</span>
                                    <span class="meta-icon">📍</span> <?php echo e($m['location']); ?>

                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if($item->body): ?>
                            <p class="card-body"><?php echo e(\Illuminate\Support\Str::limit($item->body, 120)); ?></p>
                        <?php endif; ?>

                        <div class="card-footer">
                            <?php if($item->contributor_name): ?>
                                <span class="card-author">Added by <?php echo e($item->contributor_name); ?></span>
                            <?php else: ?>
                                <span></span>
                            <?php endif; ?>
                            <span class="card-arrow">→</span>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php $__currentLoopData = ['town-council', 'board-of-education']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $sec = \App\Models\Section::where('slug', $slug)->first();
            $items = $sec ? \App\Models\Content::where('section_id', $sec->id)->where('published', true)->orderByDesc('updated_at')->limit(1)->get() : collect();
        ?>

        <?php if($items->isNotEmpty()): ?>
            <?php
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
            ?>
            <section class="section">
                <div class="section-header">
                    <div class="section-title-group">
                        <h2>Most Recent <?php echo e($sec->title); ?></h2>
                        <?php if(!empty($m['date'])): ?>
                            <p class="section-desc"><?php echo e($m['date']); ?><?php if(!empty($m['location'])): ?> · <?php echo e($m['location']); ?><?php endif; ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="section-actions">
                        <?php if(auth()->guard()->check()): ?>
                            <button type="button" class="btn-edit" data-edit="<?php echo e($editPayload); ?>" title="Edit meeting notes">✎</button>
                         <?php endif; ?>
                        <button type="button"
                                class="btn-add"
                                data-contribute="<?php echo e($slug); ?>"
                                title="Add meeting notes"
                                <?php if(auth()->guard()->guest()): ?> onclick="openAuthModal(event)" <?php endif; ?>>+</button>
                    </div>
                </div>

                <div class="meeting-card">
                    <ul class="meeting-list">
                        <?php $__currentLoopData = ($m['bullets'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bullet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($bullet); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </section>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\guilford-laravel\resources\views/home.blade.php ENDPATH**/ ?>