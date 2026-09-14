<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Moderator'); ?> — Guilford Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
</head>
<body>

<div class="admin-wrap">
    <aside class="admin-sidebar">
        <div class="brand">Moderator</div>
        <nav>
            <a href="<?php echo e(url('/moderator')); ?>" class="<?php echo e(Request::is('moderator') ? 'active' : ''); ?>">Pending Queue</a>
            <?php if(auth()->user()->isAdmin()): ?>
                <a href="<?php echo e(url('/admin')); ?>">Admin Panel</a>
            <?php endif; ?>
            <a href="<?php echo e(url('/')); ?>">← Back to Site</a>
            <form method="post" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" style="background:none;border:none;color:#C8D8E0;padding:11px 22px;font-size:.86rem;text-align:left;width:100%;cursor:pointer;font-family:inherit;border-left:3px solid transparent">
                    Log Out
                </button>
            </form>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-topbar">
            <h1><?php echo $__env->yieldContent('page-title', 'Moderator Dashboard'); ?></h1>
            <div class="admin-user">👤 <?php echo e(auth()->user()->display_name); ?></div>
        </div>

        <?php if(session('success')): ?>
            <div class="flash"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>

<script src="<?php echo e(asset('js/app.js')); ?>"></script>
</body>
</html><?php /**PATH C:\xampp\htdocs\guilford-laravel\resources\views/layouts/moderator.blade.php ENDPATH**/ ?>