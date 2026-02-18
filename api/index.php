<?php
require_once 'includes/config.php';
$page_title = 'Home';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero" id="home">
    <div class="hero-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <span class="hero-badge">Best Marketing Service</span>
                <h1 class="hero-title">It's Time to <span class="gradient-text">Upscale</span> Your Business</h1>
                <p class="hero-description">We bring solutions to make business easier. As a creative company, we prioritize fostering long-term relationships with our customers, ensuring their success is our ultimate goal.</p>
                <a href="contact.php" class="btn btn-primary btn-lg">
                    Contact Us
                    <i class="fas fa-arrow-right"></i>
                </a>
                <div class="hero-stats">
                    <div class="stat-item">
                        <h3 class="stat-number">7+</h3>
                        <p class="stat-label">Years Experience</p>
                    </div>
                    <div class="stat-item">
                        <h3 class="stat-number">200+</h3>
                        <p class="stat-label">Trusted Companies</p>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-image-wrapper">
                    <img src="https://shtheme.com/demosd/ziptech/wp-content/uploads/2023/05/home3-img1.png" alt="Hero Illustration">
                    <div class="floating-card card-1">
                        <i class="fas fa-chart-line"></i>
                        <span>Growth</span>
                    </div>
                    <div class="floating-card card-2">
                        <i class="fas fa-shield-alt"></i>
                        <span>Security</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section" id="services1">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">Our Services</span>
            <h2 class="section-title">Solutions We Provide</h2>
            <p class="section-description">Comprehensive digital solutions tailored to your business needs</p>
        </div>
        
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3 class="service-title">App Development</h3>
                <p class="service-description">Adya3 Solutions specializes in crafting cutting-edge Android and iOS applications tailored to your business needs.</p>
                <a href="services.php#app-development" class="service-link">
                    Read More
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <h3 class="service-title">Website Development</h3>
                <p class="service-description">Adya3 Solutions specializes in crafting bespoke websites tailored to elevate your online presence.</p>
                <a href="services.php#website-development" class="service-link">
                    Read More
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h3 class="service-title">Social Media Marketing</h3>
                <p class="service-description">Adya3 Solutions specializes in innovative Social Media Marketing strategies tailored to elevate your brand.</p>
                <a href="services.php#social-media-marketing" class="service-link">
                    Read More
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3 class="service-title">Online Courses</h3>
                <p class="service-description">We provide detailed Web Development courses for both Technical and Non-Technical professionals.</p>
                <a href="courses.php" class="service-link">
                    Read More
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="services.php" class="btn btn-primary">View All Services</a>
        </div>
    </div>
</section>

<!-- About Preview Section -->
<section class="about-preview">
    <div class="container">
        <div class="about-content">
            <div class="about-images">
                <div class="about-image-wrapper">
                    <img src="https://shtheme.com/demosd/ziptech/wp-content/uploads/2023/05/about3-img1.jpg" alt="About Adya3" class="about-img-1">
                    <img src="https://shtheme.com/demosd/ziptech/wp-content/uploads/2023/05/about3-img2.jpg" alt="Team" class="about-img-2">
                    <div class="experience-badge">
                        <h3>7+</h3>
                        <p>Years<br>Experience</p>
                    </div>
                </div>
            </div>
            
            <div class="about-text">
                <span class="section-badge">About Company</span>
                <h2 class="section-title">We Provide the Best Web Design Services</h2>
                <p class="about-description">More than 200+ companies are trusted. We harness the tools of traditional and digital. We are your authentic brand.</p>
                
                <div class="about-features">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="feature-content">
                            <h4>SEO Optimization</h4>
                            <p>In the shortest possible time for customers to solve questions of the use of product.</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div class="feature-content">
                            <h4>UX/UI Strategy</h4>
                            <p>Adya3 Solutions employs a user-centric UX/UI strategy to craft seamless digital experiences.</p>
                        </div>
                    </div>
                </div>
                
                <a href="about.php" class="btn btn-primary mt-3">Learn More About Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Counter Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="counter" data-target="7">0</h3>
                <p>Years Of Experience</p>
            </div>
            
            <div class="stat-box">
                <div class="stat-icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <h3 class="counter" data-target="200">0</h3>
                <p>Projects Completed</p>
            </div>
            
            <div class="stat-box">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="counter" data-target="50">0</h3>
                <p>Skilled Experts</p>
            </div>
            
            <div class="stat-box">
                <div class="stat-icon">
                    <i class="fas fa-smile"></i>
                </div>
                <h3 class="counter" data-target="200">0</h3>
                <p>Satisfied Clients</p>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="why-choose-us">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">Our Benefits</span>
            <h2 class="section-title">Why Choose Us?</h2>
        </div>
        
        <div class="benefits-grid">
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h3>High Security</h3>
                <p>Advanced security measures to protect your digital assets</p>
            </div>
            
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3>Skilled Experts</h3>
                <p>Team of experienced professionals dedicated to your success</p>
            </div>
            
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>365 Days Support</h3>
                <p>Round-the-clock support whenever you need assistance</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Transform Your Business?</h2>
            <p>Let's discuss how we can help you achieve your goals</p>
            <div class="cta-buttons">
                <a href="contact.php" class="btn btn-primary btn-lg">Get Started</a>
                <a href="services.php" class="btn btn-outline btn-lg">Our Services</a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
