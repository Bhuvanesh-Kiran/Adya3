    <!-- Footer -->
    <footer class="footer">
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <img src="https://adya3.com/wp-content/uploads/2024/11/ADYA3.png" alt="<?php echo SITE_NAME; ?>">
                    </div>
                    <p class="footer-description">We bring solutions to make business easier.</p>
                    <div class="social-links">
                        <a href="<?php echo FACEBOOK_URL; ?>" target="_blank" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="<?php echo INSTAGRAM_URL; ?>" target="_blank" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4 class="footer-title">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="courses.php">Courses</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4 class="footer-title">Services</h4>
                    <ul class="footer-links">
                        <li><a href="services.php#website-design">Website Designing</a></li>
                        <li><a href="services.php#digital-marketing">Digital Marketing</a></li>
                        <li><a href="services.php#app-development">App Development</a></li>
                        <li><a href="services.php#brochure-design">Brochure Design</a></li>
                        <li><a href="services.php#seo">SEO Services</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4 class="footer-title">Contact Info</h4>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Visakhapatnam, Andhra Pradesh</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?php echo str_replace(' ', '', SITE_PHONE); ?>"><?php echo SITE_PHONE; ?></a>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?php echo str_replace(' ', '', SITE_PHONE_2); ?>"><?php echo SITE_PHONE_2; ?></a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a>
                        </li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4 class="footer-title">Policies</h4>
                    <ul class="footer-links">
                        <li><a href="privacy-policy.php">Privacy Policy</a></li>
                        <li><a href="refund-policy.php">Refund Policy</a></li>
                        <li><a href="terms-conditions.php">Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

    <div class="floating-contact-wrapper" id="contactMenu">
    <div class="contact-options">
        <a href="https://wa.me/919030761831" class="contact-child whatsapp" target="_blank" title="WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="https://www.instagram.com/adya3_solutions/" class="contact-child instagram" target="_blank" title="Instagram">
            <i class="fab fa-instagram"></i>
        </a>
        <a href="tel:+919030761831" class="contact-child phone" title="Call Us">
            <i class="fas fa-phone-alt"></i>
        </a>
    </div>
    <button class="contact-trigger" id="contactTrigger" aria-label="Contact Options">
        <i class="fas fa-comments" id="triggerIcon"></i>
    </button>
</div>
    <!-- Custom JavaScript -->
    <script src="assets/js/main.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('contactTrigger');
    const options = document.querySelector('.contact-options');
    const icon = document.getElementById('triggerIcon');

    trigger.addEventListener('click', function() {
        options.classList.toggle('active');
        trigger.classList.toggle('active');
        
        // Toggle icon between comments and close
        if (trigger.classList.contains('active')) {
            icon.classList.remove('fa-comments');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-comments');
        }
    });

    // Close menu if user clicks outside
    document.addEventListener('click', function(event) {
        if (!document.getElementById('contactMenu').contains(event.target)) {
            options.classList.remove('active');
            trigger.classList.remove('active');
            icon.classList.replace('fa-times', 'fa-comments');
        }
    });
});
</script>
</body>
</html>
