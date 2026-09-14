

<?php $__env->startSection('title', 'Moderator'); ?>
<?php $__env->startSection('page-title', 'Pending Contributions'); ?>

<?php $__env->startSection('content'); ?>

<?php if($pending->isEmpty()): ?>
    <p style="text-align:center;color:#6B6B6B;padding:60px;background:#fff;border-radius:12px;border:1px solid #E8E4DA">
        All caught up! No pending contributions. 🎉
    </p>
<?php else: ?>
    <?php $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="mod-card">
            <div class="mod-head">
                <span class="tag"><?php echo e($p->section->title ?? $p->section->slug); ?></span>
                <span class="tag warn"><?php echo e($p->contribution_type); ?></span>
                <span>by <strong><?php echo e($p->user->display_name ?? 'Anonymous'); ?></strong> · <?php echo e($p->created_at->diffForHumans()); ?></span>
            </div>

            <pre><?php echo e(json_encode($p->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></pre>

            <form method="post" action="<?php echo e(route('moderator.moderate', $p)); ?>" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                <?php echo csrf_field(); ?>
                <input name="note" placeholder="Optional moderator note" style="flex:1;min-width:200px;padding:10px 14px;border:1.5px solid #D8D4CA;border-radius:8px;font-family:inherit">
                <button name="action" value="approved" class="btn-sm ok">✓ Approve</button>
                <button name="action" value="rejected" class="btn-sm danger">✕ Reject</button>
            </form>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.moderator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\guilford-laravel\resources\views/moderator/dashboard.blade.php ENDPATH**/ ?>