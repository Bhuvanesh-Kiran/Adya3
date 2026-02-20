// =====================================================
// ADYA3 SOLUTIONS - MAIN JAVASCRIPT
// Interactive Features & Functionality
// =====================================================

(function() {
    'use strict';

    // =====================================================
    // DOM ELEMENTS
    // =====================================================
    const header = document.getElementById('header');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.getElementById('navMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const navLinks = document.querySelectorAll('.nav-link');
    const scrollTopBtn = document.getElementById('scrollTop');

    // =====================================================
    // MOBILE MENU FUNCTIONALITY
    // =====================================================
    if (mobileMenuToggle && navMenu && mobileMenuOverlay) {
        // Toggle mobile menu
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            mobileMenuOverlay.classList.toggle('active');
            document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
        });

        // Close menu when overlay is clicked
        mobileMenuOverlay.addEventListener('click', function() {
            navMenu.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });

        // Close menu when nav link is clicked
        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
                mobileMenuOverlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
    }

    // =====================================================
    // HEADER SCROLL EFFECT
    // =====================================================
    let lastScroll = 0;

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        if (header) {
            if (currentScroll > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        }
        
        lastScroll = currentScroll;
    });

    // =====================================================
    // ACTIVE NAV LINK ON SCROLL
    // =====================================================
    const sections = document.querySelectorAll('section[id]');

    function highlightNavOnScroll() {
        const scrollY = window.pageYOffset;
        
        sections.forEach(function(section) {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop - 150;
            const sectionId = section.getAttribute('id');
            const navLink = document.querySelector('.nav-link[href*="' + sectionId + '"]');
            
            if (navLink) {
                if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                    navLinks.forEach(function(link) {
                        link.classList.remove('active');
                    });
                    navLink.classList.add('active');
                }
            }
        });
    }

    // Debounce function for performance
    function debounce(func, wait) {
        let timeout;
        return function executedFunction() {
            const context = this;
            const args = arguments;
            const later = function() {
                clearTimeout(timeout);
                func.apply(context, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    const debouncedHighlight = debounce(highlightNavOnScroll, 10);
    window.addEventListener('scroll', debouncedHighlight);

    // =====================================================
    // SMOOTH SCROLL FOR ANCHOR LINKS
    // =====================================================
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Ignore empty anchors
            if (href === '#' || href === '') {
                e.preventDefault();
                return;
            }
            
            const target = document.querySelector(href);
            
            if (target) {
                e.preventDefault();
                const headerHeight = header ? header.offsetHeight : 0;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // =====================================================
    // COUNTER ANIMATION
    // =====================================================
    const counters = document.querySelectorAll('.counter');
    let counterAnimated = false;

    function animateCounters() {
        counters.forEach(function(counter) {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 2000; // 2 seconds
            const increment = target / (duration / 16); // 60 FPS
            let current = 0;
            
            const updateCounter = function() {
                current += increment;
                
                if (current < target) {
                    counter.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            
            updateCounter();
        });
    }

    function checkCounterVisibility() {
        if (!counterAnimated) {
            const statsSection = document.querySelector('.stats-section');
            if (statsSection) {
                const rect = statsSection.getBoundingClientRect();
                const isVisible = rect.top < window.innerHeight && rect.bottom >= 0;
                
                if (isVisible) {
                    animateCounters();
                    counterAnimated = true;
                }
            }
        }
    }

    window.addEventListener('scroll', checkCounterVisibility);
    window.addEventListener('load', checkCounterVisibility);

    // =====================================================
    // SCROLL TO TOP BUTTON
    // =====================================================
    if (scrollTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        });

        scrollTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // =====================================================
    // FORM VALIDATION AND SUBMISSION
    // =====================================================
    const contactForm = document.getElementById('contactForm');
    const loginForm = document.querySelector('.login-form');

    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(contactForm);
            const submitButton = contactForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            // Validate form
            const name = formData.get('name').trim();
            const phone = formData.get('phone').trim();
            const email = formData.get('email').trim();
            const service = formData.get('service');
            
            // Basic validation
            if (!name || !phone || !email || !service) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showNotification('Please enter a valid email address', 'error');
                return;
            }
            
            // Phone validation (basic)
            const phoneRegex = /^[0-9+\-\s()]{10,}$/;
            if (!phoneRegex.test(phone)) {
                showNotification('Please enter a valid phone number', 'error');
                return;
            }
            
            // Disable submit button and show loading
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            
            try {
                // Submit form
                const response = await fetch('process-contact.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification(result.message || 'Thank you! We will contact you soon.', 'success');
                    contactForm.reset();
                } else {
                    showNotification(result.message || 'Something went wrong. Please try again.', 'error');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                showNotification('Unable to submit form. Please try again later.', 'error');
            } finally {
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });
    }

    // =====================================================
    // NOTIFICATION SYSTEM
    // =====================================================
    function showNotification(message, type) {
        // Remove existing notification if any
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'notification notification-' + type;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Add styles if not already added
        if (!document.querySelector('#notification-styles')) {
            const style = document.createElement('style');
            style.id = 'notification-styles';
            style.textContent = `
                .notification {
                    position: fixed;
                    top: 100px;
                    right: 20px;
                    min-width: 300px;
                    max-width: 500px;
                    background: white;
                    padding: 1rem 1.5rem;
                    border-radius: 12px;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
                    z-index: 10000;
                    animation: slideInRight 0.3s ease;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 1rem;
                }
                
                .notification-success {
                    border-left: 4px solid #10b981;
                }
                
                .notification-error {
                    border-left: 4px solid #ef4444;
                }
                
                .notification-info {
                    border-left: 4px solid #6366f1;
                }
                
                .notification-content {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    flex: 1;
                }
                
                .notification-content i {
                    font-size: 1.25rem;
                }
                
                .notification-success .notification-content i {
                    color: #10b981;
                }
                
                .notification-error .notification-content i {
                    color: #ef4444;
                }
                
                .notification-info .notification-content i {
                    color: #6366f1;
                }
                
                .notification-close {
                    background: none;
                    border: none;
                    color: #64748b;
                    cursor: pointer;
                    padding: 0.25rem;
                    font-size: 1rem;
                    transition: color 0.2s;
                }
                
                .notification-close:hover {
                    color: #1e293b;
                }
                
                @keyframes slideInRight {
                    from {
                        transform: translateX(400px);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                
                @keyframes slideOutRight {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(400px);
                        opacity: 0;
                    }
                }
                
                @media (max-width: 768px) {
                    .notification {
                        right: 10px;
                        left: 10px;
                        min-width: auto;
                        top: 80px;
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        // Add to document
        document.body.appendChild(notification);
        
        // Close button functionality
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', function() {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(function() {
                notification.remove();
            }, 300);
        });
        
        // Auto remove after 5 seconds
        setTimeout(function() {
            if (notification.parentElement) {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(function() {
                    notification.remove();
                }, 300);
            }
        }, 5000);
    }

    // =====================================================
    // INTERSECTION OBSERVER FOR ANIMATIONS
    // =====================================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-on-scroll');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        const elementsToAnimate = document.querySelectorAll(
            '.service-card, .feature-item, .process-step, .benefit-card, .stat-box, .course-card, .mv-card, .team-member'
        );
        
        elementsToAnimate.forEach(function(el) {
            observer.observe(el);
        });
    });

    // =====================================================
    // LANGUAGE SELECTOR
    // =====================================================
    const languageSelect = document.getElementById('languageSelect');

    if (languageSelect) {
        languageSelect.addEventListener('change', function(e) {
            const selectedLanguage = e.target.value;
            const languageName = e.target.options[e.target.selectedIndex].text;
            
            console.log('Language changed to:', selectedLanguage);
            
            // In a real application, you would implement language switching here
            // For now, just show a notification
            showNotification('Language changed to ' + languageName, 'info');
        });
    }

    // =====================================================
    // FORM INPUT ENHANCEMENTS
    // =====================================================
    const formInputs = document.querySelectorAll('.form-group input, .form-group select, .form-group textarea');

    formInputs.forEach(function(input) {
        // Add focus effect
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
        
        // Add filled class if input has value
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.parentElement.classList.add('filled');
            } else {
                this.parentElement.classList.remove('filled');
            }
        });
    });

    // =====================================================
    // LAZY LOADING IMAGES
    // =====================================================
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    
                    observer.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(function(img) {
            imageObserver.observe(img);
        });
    }

    // =====================================================
    // PREVENT FORM RESUBMISSION ON PAGE RELOAD
    // =====================================================
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    // =====================================================
    // PAGE LOAD ANIMATIONS
    // =====================================================
    window.addEventListener('load', function() {
        // Add loaded class to body
        document.body.classList.add('loaded');
        
        // Animate hero section
        const heroText = document.querySelector('.hero-text');
        const heroImage = document.querySelector('.hero-image');
        
        if (heroText) {
            heroText.style.animation = 'fadeInUp 0.8s ease forwards';
        }
        
        if (heroImage) {
            heroImage.style.animation = 'fadeInUp 0.8s ease 0.2s forwards';
            heroImage.style.opacity = '0';
            setTimeout(function() {
                heroImage.style.opacity = '1';
            }, 200);
        }
    });

    // =====================================================
    // CONSOLE BRANDING
    // =====================================================
    console.log('%c Adya3 Solutions ', 'background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%); color: white; font-size: 20px; padding: 10px 20px; border-radius: 5px;');
    console.log('%c We bring solutions to make business easier. ', 'font-size: 14px; color: #6366f1;');
    console.log('%c Visit: https://www.adya3.com ', 'font-size: 12px; color: #64748b;');

    // =====================================================
    // ERROR HANDLING
    // =====================================================
    window.addEventListener('error', function(e) {
        console.error('An error occurred:', e.error);
    });

    // =====================================================
    // DASHBOARD SPECIFIC FUNCTIONALITY
    // =====================================================
    
    // Check if we're on the dashboard page
    const dashboardSection = document.querySelector('.dashboard-section');
    
    if (dashboardSection) {
        // Animate progress bars
        const progressBars = document.querySelectorAll('.progress-fill');
        
        const animateProgressBars = function() {
            progressBars.forEach(function(bar) {
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(function() {
                    bar.style.width = width;
                }, 100);
            });
        };
        
        // Trigger animation when in view
        const progressObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateProgressBars();
                    progressObserver.disconnect();
                }
            });
        });
        
        const progressSection = document.querySelector('.course-progress-grid');
        if (progressSection) {
            progressObserver.observe(progressSection);
        }
    }

    // =====================================================
    // ACCESSIBILITY ENHANCEMENTS
    // =====================================================
    
    // Add keyboard navigation for custom elements
    document.querySelectorAll('[role="button"]').forEach(function(element) {
        element.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });

    // Focus management for modals/overlays
    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                navMenu.classList.remove('active');
                mobileMenuOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // =====================================================
    // UTILITY FUNCTIONS
    // =====================================================
    
    // Check if element is in viewport
    function isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
    
    // Get scroll position
    function getScrollPosition() {
        return window.pageYOffset || document.documentElement.scrollTop;
    }
    
    // Smooth scroll to element
    function scrollToElement(element, offset) {
        offset = offset || 0;
        const elementPosition = element.getBoundingClientRect().top + window.pageYOffset;
        const offsetPosition = elementPosition - offset;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }

    // =====================================================
    // PERFORMANCE MONITORING (Optional)
    // =====================================================
    
    // Log page load time
    window.addEventListener('load', function() {
        if (window.performance) {
            const perfData = window.performance.timing;
            const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
            console.log('Page load time:', pageLoadTime + 'ms');
        }
    });

})();
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