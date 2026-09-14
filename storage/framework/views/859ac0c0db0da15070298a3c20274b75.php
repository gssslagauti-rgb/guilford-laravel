

<?php $__env->startSection('title', 'Sections'); ?>
<?php $__env->startSection('page-title', 'Sections'); ?>

<?php $__env->startSection('content'); ?>

<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Slug</th>
            <th>Title</th>
            <th>Description</th>
            <th>Type</th>
            <th>Order</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($s->id); ?></td>
                <td><code><?php echo e($s->slug); ?></code></td>
                <td><strong><?php echo e($s->title); ?></strong></td>
                <td><?php echo e($s->description ?? '—'); ?></td>
                <td>
                    <?php if($s->is_meeting): ?>
                        <span class="tag warn">Meeting</span>
                    <?php else: ?>
                        <span class="tag">Standard</span>
                    <?php endif; ?>
                </td>
                <td><?php echo e($s->order_index); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\guilford-laravel\resources\views/admin/sections.blade.php ENDPATH**/ ?>