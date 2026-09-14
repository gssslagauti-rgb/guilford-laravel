<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Guilford Hub'); ?> — Guilford Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="<?php echo e(url('/')); ?>" class="logo">
            <span class="logo-mark">G</span>
            <span class="logo-text">Guilford Hub</span>
        </a>

        <nav class="main-nav">
            <a href="<?php echo e(url('/')); ?>">Home</a>
            <a href="<?php echo e(route('section', 'events')); ?>">Upcoming Events</a>
            <a href="<?php echo e(route('section', 'projects')); ?>">Ongoing Town Projects</a>
            <a href="<?php echo e(route('section', 'town-council')); ?>">Town Council Meeting</a>
            <a href="<?php echo e(route('section', 'board-of-education')); ?>">Board of Education Meeting</a>
            <a href="<?php echo e(route('section', 'sports')); ?>">Sports &amp; Registration</a>
            <a href="<?php echo e(route('section', 'construction')); ?>">Construction &amp; Permits</a>
        </nav>

        <div class="auth-controls">
            <?php if(auth()->guard()->check()): ?>
                <span class="user-badge">👤 <?php echo e(auth()->user()->display_name); ?></span>

                <?php if(auth()->user()->isModerator()): ?>
                    <a href="<?php echo e(url('/moderator')); ?>" class="btn-ghost">Moderate</a>
                <?php endif; ?>

                <?php if(auth()->user()->isAdmin()): ?>
                    <a href="<?php echo e(url('/admin')); ?>" class="btn-ghost">Admin</a>
                <?php endif; ?>

                <form method="post" action="<?php echo e(route('logout')); ?>" style="display:inline">
                    <?php echo csrf_field(); ?>
                    <button class="btn-ghost">Log out</button>
                </form>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="btn-ghost">Sign In</a>
                <a href="<?php echo e(route('register')); ?>" class="btn-primary">Join</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if(session('success')): ?>
    <div class="flash"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<?php if($errors->any()): ?>
    <div class="flash err"><?php echo e($errors->first()); ?></div>
<?php endif; ?>

<main>
    <?php echo $__env->yieldContent('content'); ?>
</main>


<?php if(auth()->guard()->check()): ?>
<div id="contribution-modal" class="modal hidden">
    <div class="modal-content">
        <button type="button" class="modal-close" onclick="document.getElementById('contribution-modal').classList.add('hidden')">&times;</button>
        <h2 id="modal-title">Contribute</h2>
        <p class="modal-sub">Your submission will be reviewed by a moderator before appearing.</p>

        <form action="<?php echo e(route('contribute')); ?>" method="post" id="contribution-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="section" id="c-section">
            <input type="hidden" name="contribution_type" id="c-type" value="new">
            <input type="hidden" name="target_id" id="c-target">
            <div id="contribution-fields"></div>
            <button type="submit" class="btn-primary" id="modal-submit" style="width:100%;margin-top:8px">Submit for Review</button>
        </form>
    </div>
</div>
<?php endif; ?>


<?php if(auth()->guard()->guest()): ?>
<div id="auth-modal" class="modal hidden">
    <div class="modal-content modal-narrow">
        <button type="button" class="modal-close" onclick="document.getElementById('auth-modal').classList.add('hidden')">&times;</button>
        <h2>Join Guilford Hub</h2>
        <p class="modal-sub">You need an account to contribute. It takes 30 seconds.</p>

        <div class="auth-tabs">
            <a href="<?php echo e(route('login')); ?>" class="auth-tab">Sign In</a>
            <a href="<?php echo e(route('register')); ?>" class="auth-tab active">Register</a>
        </div>

        <form method="post" action="<?php echo e(route('register')); ?>" class="auth-form">
            <?php echo csrf_field(); ?>
            <label>Display Name</label>
            <input type="text" name="display_name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password (8+ characters)</label>
            <input type="password" name="password" required minlength="8">

            <button type="submit" class="btn-primary" style="width:100%">Create Account</button>
        </form>

        <p class="modal-foot">Already have an account? <a href="<?php echo e(route('login')); ?>">Sign in</a></p>
    </div>
</div>
<?php endif; ?>

<?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\guilford-laravel\resources\views/layouts/header.blade.php ENDPATH**/ ?>