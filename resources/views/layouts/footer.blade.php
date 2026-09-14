<section class="subscribe-banner">
    <div class="container">
        <h3>Stay in the loop</h3>
        <p>Get weekly Guilford updates delivered to your inbox.</p>
        <form action="{{ route('subscribe') }}" method="post" class="subscribe-form">
            @csrf
            <input type="email" name="email" placeholder="you@example.com" required>
            <button type="submit" class="btn-primary">Subscribe</button>
        </form>
    </div>
</section>

<footer class="site-footer">
    <div class="container">Guilford Hub · Community-built, community-moderated</div>
</footer>

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>