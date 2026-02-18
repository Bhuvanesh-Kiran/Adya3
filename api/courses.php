<?php
require_once 'includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 

// Handle Add to Cart Logic
if (isset($_POST['add_to_course_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: learning-dashboard.php?error=login_required");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $id = sanitize($_POST['course_id']);
    $name = sanitize($_POST['course_name']);
    $price = sanitize($_POST['course_price']);

    // Check if item already exists in DB cart
    $check = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND course_id = ?");
    $check->bind_param("ii", $user_id, $id);
    $check->execute();
    if ($check->get_result()->num_rows == 0) {
        $ins = $conn->prepare("INSERT INTO cart (user_id, course_id, course_name, course_price) VALUES (?, ?, ?, ?)");
        $ins->bind_param("iisd", $user_id, $id, $name, $price);
        $ins->execute();
    }

    // Sync session with DB cart
    $_SESSION['cart'][$id] = ['name' => $name, 'price' => $price, 'quantity' => 1];

    header("Location: cart.php");
    exit();
}

$page_title = 'Our Courses';
require_once 'includes/header.php';

// Fetch all courses, newest first
$query = "SELECT * FROM courses ORDER BY id DESC";
$result = $conn->query($query);
?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Our Courses</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Courses</li>
                </ol>
            </nav>
        </div>
    </div>
</section>

<section class="courses-section">
    <div class="container">
        <div class="courses-grid">
            <?php while($course = $result->fetch_assoc()): ?>
                <div class="course-card">
                    <a href="course-details.php?id=<?php echo $course['id']; ?>">
                        <div class="course-image">
                            <img src="<?php echo $course['image_path']; ?>" 
                            onerror="this.src='assets/images/placeholder.jpg';"
                            style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                        </div>    
                    </a>
                    <div class="course-content">
                        <span class="category-badge" style="font-size: 0.75rem; color: #6366f1; font-weight: 600; text-transform: uppercase;">
                            <?php echo htmlspecialchars($course['category'] ?? 'Learning'); ?>
                        </span>
                        <h3 class="course-title mt-1" style="font-size: 1.25rem; margin-bottom: 15px;">
                            <?php echo htmlspecialchars($course['title']); ?>
                        </h3>
                        
                        <div class="course-footer">
                            <div class="course-price-wrap">
                                <?php 
                                    // Handle logic for new/old prices to match DB
                                    $new_price = (float)($course['new_price'] ?? 0);
                                    $old_price = $new_price * 1.5; // Mockup for 'original' price display
                                ?>
                                <span class="old-price text-muted" style="text-decoration: line-through; font-size: 0.85rem;">
                                    ₹<?php echo number_format($old_price, 2); ?>
                                </span>
                                <span class="new-price d-block" style="color: #10b981; font-weight: 700; font-size: 1.2rem;">
                                    ₹<?php echo number_format($new_price, 2); ?>
                                </span>
                            </div>
                            
                            <?php if (isset($_SESSION['cart'][$course['id']])): ?>
                                <a href="cart.php" class="btn-cart view-cart-btn" style="background: #6366f1; color: white;">View Cart</a>
                            <?php else: ?>
                                <form method="POST">
                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                    <input type="hidden" name="course_name" value="<?php echo htmlspecialchars($course['title']); ?>">
                                    <input type="hidden" name="course_price" value="<?php echo $new_price; ?>">
                                    <button type="submit" name="add_to_course_cart" class="btn-cart">Add to Cart</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<section class="course-benefits" style="background: #f8fafc; padding: 80px 0;">
    <div class="container">
        <div class="section-header text-center mb-5">
            <span class="section-badge">Why Choose Our Courses</span>
            <h2 class="section-title">Learning Benefits</h2>
        </div>
        
        <div class="benefits-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <div class="benefit-item text-center p-4 bg-white rounded shadow-sm">
                <div class="benefit-icon mb-3" style="font-size: 2.5rem; color: #6366f1;"><i class="fas fa-chalkboard-teacher"></i></div>
                <h4 class="fw-bold">Expert Instructors</h4>
                <p class="text-muted small">Learn from industry professionals with real-world experience.</p>
            </div>
            
            <div class="benefit-item text-center p-4 bg-white rounded shadow-sm">
                <div class="benefit-icon mb-3" style="font-size: 2.5rem; color: #10b981;"><i class="fas fa-laptop-code"></i></div>
                <h4 class="fw-bold">Hands-On Projects</h4>
                <p class="text-muted small">Build real-world projects to strengthen your portfolio.</p>
            </div>

            <div class="benefit-item text-center p-4 bg-white rounded shadow-sm">
                <div class="benefit-icon mb-3" style="font-size: 2.5rem; color: #f59e0b;"><i class="fas fa-certificate"></i></div>
                <h4 class="fw-bold">Certification</h4>
                <p class="text-muted small">Receive industry-recognized certificates upon completion.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>