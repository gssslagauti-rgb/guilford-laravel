

<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<div class="stat-grid">
    <div class="stat">
        <div class="stat-num"><?php echo e($stats['users']); ?></div>
        <div class="stat-label">Total Users</div>
    </div>
    <div class="stat">
        <div class="stat-num"><?php echo e($stats['moderators']); ?></div>
        <div class="stat-label">Moderators</div>
    </div>
    <div class="stat warn">
        <div class="stat-num"><?php echo e($stats['pending']); ?></div>
        <div class="stat-label">Pending Review</div>
    </div>
    <div class="stat">
        <div class="stat-num"><?php echo e($stats['content']); ?></div>
        <div class="stat-label">Published Items</div>
    </div>
    <div class="stat">
        <div class="stat-num"><?php echo e($stats['subscribers']); ?></div>
        <div class="stat-label">Subscribers</div>
    </div>
</div>

<h2>Recent Content</h2>
<table class="data-table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Section</th>
            <th>Updated</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $recentContent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><strong><?php echo e($c->title); ?></strong></td>
                <td><span class="tag"><?php echo e($c->section->title ?? '—'); ?></span></td>
                <td><?php echo e($c->updated_at->diffForHumans()); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="3" style="text-align:center;padding:30px;color:#6B6B6B">No content yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\guilford-laravel\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>