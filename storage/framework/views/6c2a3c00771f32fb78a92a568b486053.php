<section class="subscribe-banner">
    <div class="container">
        <h3>Stay in the loop</h3>
        <p>Get weekly Guilford updates delivered to your inbox.</p>
        <form action="<?php echo e(route('subscribe')); ?>" method="post" class="subscribe-form">
            <?php echo csrf_field(); ?>
            <input type="email" name="email" placeholder="you@example.com" required>
            <button type="submit" class="btn-primary">Subscribe</button>
        </form>
    </div>
</section>

<footer class="site-footer">
    <div class="container">Guilford Hub · Community-built, community-moderated</div>
</footer>

<script src="<?php echo e(asset('js/app.js')); ?>"></script>
</body>
</html><?php /**PATH C:\xampp\htdocs\guilford-laravel\resources\views/layouts/footer.blade.php ENDPATH**/ ?>