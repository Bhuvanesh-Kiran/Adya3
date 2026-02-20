<?php
if (!defined('SITE_NAME')) {
    require_once 'config.php';
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Adya3 Solutions - We bring solutions to make business easier. Expert website design, app development, and digital marketing services.">
    <meta name="keywords" content="web design, app development, digital marketing, SEO, UI/UX">
    <meta name="author" content="Adya3 Solutions">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME . ' - Website Designing & Development'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=1.18">
    <link rel="stylesheet" href="assets/css/responsive.css?v=1.8">
</head>
<body>
    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Header -->
    <header class="header" id="header">
        <div class="header-top">
            <div class="container">
                <div class="header-top-content">
                    <div class="contact-info">
                        <a href="mailto:<?php echo SITE_EMAIL; ?>" class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span><?php echo SITE_EMAIL; ?></span>
                        </a>
                        <a href="tel:<?php echo str_replace(' ', '', SITE_PHONE); ?>" class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span><?php echo SITE_PHONE; ?></span>
                        </a>
                        <span class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Visakhapatnam, Andhra Pradesh</span>
                        </span>
                    </div>
                    <div class="language-selector">
                        <select id="languageSelect">
                            <option value="en">English</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
       <nav class="navbar">
    <div class="container">
        <div class="navbar-content">
            <div class="logo">
                <a href="index.php">
                    <img src="https://adya3.com/wp-content/uploads/2024/11/ADYA3.png" alt="<?php echo SITE_NAME; ?> Logo">
                </a>
            </div>
            
            <div class="nav-menu ms-auto" id="navMenu">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>" href="about.php">About</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'services.php') ? 'active' : ''; ?>" href="services.php">Services</a>
        </li>
        
        <?php 
            $course_pages = ['courses.php', 'course-details.php', 'cart.php', 'checkout.php', 'watch.php'];
            $is_course_active = in_array($current_page, $course_pages);
        ?>
        <li class="nav-item">
            <a class="nav-link <?php echo ($is_course_active) ? 'active' : ''; ?>" href="courses.php">Courses</a>
        </li>

        <?php if(!isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'learning-dashboard.php') ? 'active' : ''; ?>" href="learning-dashboard.php">
                    <i class="fas fa-user-circle me-1"></i> Login
                </a>
            </li>
        <?php else: ?>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'admin-dashboard.php') ? 'active' : ''; ?>" href="admin-dashboard.php">Admin Dashboard</a>
                </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'learning-dashboard.php') ? 'active' : ''; ?>" href="learning-dashboard.php">Learning Dashboard</a>
            </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact Us</a>
        </li>
    </ul>
        </div>
        <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle Menu">
                <span></span><span></span><span></span>
            </button>
    </div>
    </div>
</nav>
    </header>
