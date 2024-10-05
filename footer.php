<a href="https://wa.me/<?php echo do_shortcode('[display_phone]') ; ?>" class="whatsapp-icon" target="_blank" aria-label="Chat with us on WhatsApp">
    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" />
</a>

<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <p>&copy; <?php echo date("Y"); ?> Hamza photography. All rights reserved.</p>
            <div class="social-links">
                <?php echo do_shortcode('[display_social_media]') ; ?>
            </div>
        </div>
    </div>
</footer>


<?php wp_footer(); ?>
</body>
</html>