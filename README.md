# Adya3 Solutions - Complete Dynamic PHP Website

A modern, fully responsive, dynamic PHP website with separate pages for Home, About, Services, Courses, Learning Dashboard, and Contact.

## 📁 File Structure

```
adya3-website/
├── index.php                  # Homepage
├── about.php                  # About Us page
├── services.php               # Services page
├── courses.php                # Courses listing page  
├── learning-dashboard.php     # Student dashboard
├── contact.php                # Contact page with form
├── process-contact.php        # Form processing script
├── includes/
│   ├── config.php            # Site-wide configuration
│   ├── header.php            # Common header
│   └── footer.php            # Common footer
├── assets/
│   ├── css/
│   │   ├── style.css         # Main stylesheet (to be created)
│   │   └── responsive.css    # Responsive styles (to be created)
│   └── js/
│       └── main.js           # JavaScript functionality (to be created)
└── README.md                 # This file
```

## 🚀 Features

### ✨ Design & UI
- Modern gradient-based design
- Fully responsive (Mobile, Tablet, Desktop)
- Smooth animations and transitions
- Professional color scheme
- Clean, intuitive navigation

### 📄 Pages Included

1. **Home (index.php)**
   - Hero section with CTA
   - Services preview
   - About preview
   - Statistics counter
   - Benefits section
   - Call-to-action

2. **About Us (about.php)**
   - Company information
   - Mission & Vision
   - Team section
   - Statistics
   - Working process
   - Full feature showcase

3. **Services (services.php)**
   - Website Development
   - App Development  
   - Digital Marketing
   - SEO Services
   - Brochure Design
   - Logo Design
   - Detailed service descriptions with features

4. **Courses (courses.php)**
   - Full Stack Web Development
   - Mobile App Development
   - Digital Marketing Masterclass
   - UI/UX Design
   - Python Programming
   - Database Management
   - Course details with duration, level, features

5. **Learning Dashboard (learning-dashboard.php)**
   - Login page (when not logged in)
   - Student dashboard (when logged in)
   - Course progress tracking
   - Statistics overview
   - Recent activity feed
   - Sidebar navigation

6. **Contact Us (contact.php)**
   - Contact form with validation
   - Contact information
   - Office address
   - Google Maps integration
   - Social media links

### 🔧 Technical Features

- **Dynamic PHP Structure**: Modular includes for header/footer
- **Configuration File**: Centralized settings in config.php
- **Form Processing**: Professional email handling
- **Responsive Design**: Mobile-first approach
- **SEO Friendly**: Proper meta tags and structure
- **Fast Loading**: Optimized assets
- **Security**: Input sanitization and validation

## 📥 Installation

### Requirements
- Web server (Apache/Nginx)
- PHP 7.0 or higher
- Mail server configured (for contact form)

### Steps

1. **Upload Files**
   ```bash
   # Upload all files to your web server
   # Directory: /var/www/html/ or public_html/
   ```

2. **Configure Settings**
   Edit `includes/config.php`:
   ```php
   define('SITE_NAME', 'Your Company Name');
   define('SITE_EMAIL', 'your-email@domain.com');
   define('SITE_PHONE', 'Your Phone');
   // Update other settings as needed
   ```

3. **Set Permissions**
   ```bash
   chmod 755 *.php
   chmod 755 includes/*.php
   chmod 644 assets/css/*.css
   chmod 644 assets/js/*.js
   ```

4. **Test the Website**
   - Open your browser
   - Navigate to your domain
   - Test all pages and forms

## 🎨 Customization Guide

### Colors
Edit CSS variables in `assets/css/style.css`:
```css
:root {
    --primary-color: #6366f1;      /* Main brand color */
    --secondary-color: #ec4899;    /* Accent color */
    /* Modify as needed */
}
```

### Content
- **Logo**: Update image URL in `includes/header.php`
- **Contact Info**: Edit in `includes/config.php`
- **Services**: Modify content in `services.php`
- **Courses**: Update course details in `courses.php`

### Navigation
Add/remove menu items in `includes/header.php`:
```php
<li><a href="your-page.php" class="nav-link">Your Page</a></li>
```

## 📧 Email Configuration

The contact form uses PHP's `mail()` function by default.

### For Production (SMTP)
Install PHPMailer and update `process-contact.php`:
```php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-app-password';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
```

## 🎯 Page-Specific Features

### Home Page
- Animated hero section
- Service cards with hover effects
- Statistics counter animation
- Floating elements
- Call-to-action sections

### About Page
- Image gallery with experience badge
- Mission/Vision cards
- Team member showcase
- Feature highlights
- Working process timeline

### Services Page
- Detailed service descriptions
- Feature lists with icons
- Alternating layout design
- Contact CTAs
- Service-specific benefits

### Courses Page
- Course cards with badges
- Duration and level indicators
- Feature checklists
- Enrollment buttons
- Benefits section

### Learning Dashboard
- Conditional display (login/dashboard)
- Progress tracking
- Course statistics
- Activity feed
- Sidebar navigation
- Responsive dashboard layout

### Contact Page
- Multi-field contact form
- Form validation (client & server)
- Contact information display
- Google Maps embed
- Auto-reply emails
- Success/error notifications

## 🔐 Security Features

- Input sanitization
- XSS protection
- SQL injection prevention (if using database)
- CSRF protection (recommended to add)
- Email validation
- Phone number validation

## 📱 Responsive Breakpoints

- **Desktop**: 1200px+
- **Tablet**: 768px - 1199px
- **Mobile**: < 768px

## 🌐 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## 🔨 Development

### Adding a New Page

1. Create `your-page.php`:
```php
<?php
require_once 'includes/config.php';
$page_title = 'Your Page Title';
require_once 'includes/header.php';
?>

<!-- Your content here -->

<?php require_once 'includes/footer.php'; ?>
```

2. Add to navigation in `includes/header.php`
3. Update footer links if needed

### Adding a New Service

Edit `services.php` and add:
```php
<div class="service-detail" id="your-service">
    <div class="service-detail-content">
        <!-- Service content -->
    </div>
</div>
```

## 📝 To-Do / Enhancement Ideas

- [ ] Complete CSS files (style.css, responsive.css)
- [ ] Add JavaScript for interactions (main.js)
- [ ] Implement user authentication for dashboard
- [ ] Add database integration for storing submissions
- [ ] Create admin panel
- [ ] Add course enrollment system
- [ ] Implement payment gateway
- [ ] Add blog section
- [ ] Create portfolio/gallery page
- [ ] Add testimonials section
- [ ] Implement live chat
- [ ] Add newsletter subscription
- [ ] Create sitemap
- [ ] Add Google Analytics

## 🐛 Troubleshooting

### Contact Form Not Sending
1. Check PHP mail configuration
2. Verify SMTP settings
3. Check spam folder
4. Review server error logs

### Pages Not Loading
1. Verify file permissions
2. Check PHP error logs
3. Ensure includes path is correct
4. Verify PHP version compatibility

### Styling Issues
1. Clear browser cache
2. Check CSS file paths
3. Verify CSS is being loaded
4. Use browser developer tools

## 📞 Support

For questions or support:
- **Email**: contact@adya3.com
- **Phone**: +91 9030761831 / 9381389350
- **Website**: www.adya3.com

## 📄 License

© 2025 Adya3 Solutions. All rights reserved.

## 🙏 Credits

- **Fonts**: Google Fonts (Inter, Poppins)
- **Icons**: Font Awesome 6.5.1
- **Framework**: Custom PHP/CSS/JavaScript

---

**Version**: 1.0.0  
**Last Updated**: February 2025  
**Author**: Adya3 Solutions Development Team
