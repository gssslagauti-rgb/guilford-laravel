

<?php $__env->startSection('title', 'Users'); ?>
<?php $__env->startSection('page-title', 'User Management'); ?>

<?php $__env->startSection('content'); ?>

<h2>Create New User</h2>
<form method="post" action="<?php echo e(route('admin.users.create')); ?>" class="grid-2">
    <?php echo csrf_field(); ?>
    <input name="display_name" placeholder="Display Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input name="password" placeholder="Password (6+ chars)" required minlength="6">
    <select name="role">
        <option value="user">User</option>
        <option value="moderator">Moderator</option>
        <option value="admin">Admin</option>
    </select>
    <button class="btn-primary" style="grid-column:1/-1">Create User</button>
</form>

<h2>All Users (<?php echo e($users->count()); ?>)</h2>
<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($u->id); ?></td>
                <td><strong><?php echo e($u->display_name); ?></strong></td>
                <td><?php echo e($u->email); ?></td>
                <td>
                    <?php if($u->role === 'admin'): ?>
                        <span class="tag warn">Admin</span>
                    <?php elseif($u->role === 'moderator'): ?>
                        <span class="tag ok">Moderator</span>
                    <?php else: ?>
                        <span class="tag">User</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($u->is_banned): ?>
                        <span class="tag warn">Banned</span>
                    <?php else: ?>
                        <span class="tag ok">Active</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($u->id !== auth()->id()): ?>
                        <form method="post" action="<?php echo e(route('admin.users.ban', $u)); ?>" style="display:inline">
                            <?php echo csrf_field(); ?>
                            <button class="btn-sm"><?php echo e($u->is_banned ? 'Unban' : 'Block'); ?></button>
                        </form>
                        <form method="post" action="<?php echo e(route('admin.users.delete', $u)); ?>" style="display:inline" onsubmit="return confirm('Delete this user?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button class="btn-sm danger">Delete</button>
                        </form>
                    <?php else: ?>
                        <em style="color:#999">(you)</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\guilford-laravel\resources\views/admin/users.blade.php ENDPATH**/ ?>