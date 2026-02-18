<?php
require_once 'includes/config.php';
$page_title = 'Contact Us';
require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Contact Us</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
                </ol>
            </nav>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <div class="contact-content">
            <div class="contact-info-wrapper">
                <span class="section-badge">Get Quick Support</span>
                <h2 class="section-title">Request a Call Back</h2>
                <p class="contact-description">We are here to answer any question you may have. Feel free to reach out to us through any of the following methods.</p>
                
                <div class="contact-details">
                    <div class="contact-detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="detail-content">
                            <h4>Call Us Now</h4>
                            <a href="tel:<?php echo str_replace(' ', '', SITE_PHONE); ?>"><?php echo SITE_PHONE; ?></a>
                            <a href="tel:<?php echo str_replace(' ', '', SITE_PHONE_2); ?>"><?php echo SITE_PHONE_2; ?></a>
                        </div>
                    </div>
                    
                    <div class="contact-detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="detail-content">
                            <h4>Email Us</h4>
                            <a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a>
                        </div>
                    </div>
                    
                    <div class="contact-detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="detail-content">
                            <h4>Office Address</h4>
                            <p><?php echo SITE_ADDRESS; ?></p>
                        </div>
                    </div>
                    
                    <div class="contact-detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="detail-content">
                            <h4>Working Hours</h4>
                            <p>Monday - Saturday: 9:00 AM - 6:00 PM</p>
                            <p>Sunday: Closed</p>
                        </div>
                    </div>
                </div>
                
                <div class="social-links-contact">
                    <h4>Follow Us</h4>
                    <div class="social-icons">
                        <a href="<?php echo FACEBOOK_URL; ?>" target="_blank" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="<?php echo INSTAGRAM_URL; ?>" target="_blank" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-wrapper">
                <h3>Send Us a Message</h3>
                <form class="contact-form" id="contactForm" action="process-contact.php" method="POST">
                    <div class="form-group">
                        <label for="name">Full Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" placeholder="Enter your name" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number <span class="required">*</span></label>
                            <input type="tel" id="phone" name="phone" placeholder="Enter your phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address <span class="required">*</span></label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="service">Service Required <span class="required">*</span></label>
                        <select id="service" name="service" required>
                            <option value="">Select a service</option>
                            <option value="website">Website Designing and Development</option>
                            <option value="digital-marketing">Digital Marketing</option>
                            <option value="brochure">Brochure Designing</option>
                            <option value="app-development">App Development</option>
                            <option value="logo-design">Logo Design</option>
                            <option value="seo">SEO Services</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Tell us about your project..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        Submit Request
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Map Section (Optional) -->
<section class="map-section">
    <div class="container-fluid p-0">
        <div class="map-wrapper">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3799.6674885244715!2d83.21935631487873!3d17.73095998783401!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a39431389e6b0c7%3A0x321e6a6b85e6a6b8!2sVisakhapatnam%2C%20Andhra%20Pradesh!5e0!3m2!1sen!2sin!4v1234567890" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>
<script>
(function() {
    // 1. Wait for the page to be fully interactive
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('contactForm');
        if (!form) return;

        const btn = form.querySelector('button[type="submit"]');
        const originalText = 'Submit Request <i class="fas fa-paper-plane"></i>';

        // 2. FORCE reset button state on page load
        btn.disabled = false;
        btn.innerHTML = originalText;

        // 3. Track submission state to block duplicates
        let isProcessing = false;

        form.onsubmit = function(e) {
            e.preventDefault();
            
            // If already processing, kill any further action
            if (isProcessing) return false;
            
            isProcessing = true;
            btn.disabled = true;
            btn.innerHTML = 'Sending... <i class="fas fa-spinner fa-spin"></i>';

            const formData = new FormData(form);

            fetch('process-contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    form.reset();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Connection error. Please try again.');
            })
            .finally(() => {
                // Reset flag and button so user can try again if it failed
                isProcessing = false;
                btn.disabled = false;
                btn.innerHTML = originalText;
            });

            return false;
        };
    });
})();
</script>
<?php require_once 'includes/footer.php'; ?>
