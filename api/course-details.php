<?php
require_once 'includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 

// 1. Get Course ID from URL
$course_id = isset($_GET['id']) ? $_GET['id'] : 101;

// 2. Fetch Dynamic Course Data
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

// Redirect if course doesn't exist
if (!$course) {
    header("Location: courses.php");
    exit();
}

// 3. Check if specific course is in cart
$is_in_cart = false;
if (isset($_SESSION['cart']) && isset($_SESSION['cart'][$course_id])) {
    $is_in_cart = true;
}

// 4. Check Access Status (Paid User or Admin)
$has_access = false; 
if (isset($_SESSION['user_id'])) {
    // Admins always have access
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $has_access = true;
    } else {
        // Check if student has a completed enrollment
        $check_paid = $conn->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ? AND payment_status = 'completed'");
        $check_paid->bind_param("ii", $_SESSION['user_id'], $course_id);
        $check_paid->execute();
        $has_access = ($check_paid->get_result()->num_rows > 0);
        $check_paid->close();
    }
}
// Legacy support for your existing $has_paid checks
$has_paid = $has_access; 

$page_title = $course['title'];
require_once 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Course Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($course['title']); ?></li>
                </ol>
            </nav>
        </div>
    </div>
</section>

<div class="course-details-page">
    <div class="container">
        <div class="course-header-area">
            <span class="category-badge"><?php echo htmlspecialchars($course['category']); ?></span>
            <h1 class="course-main-title"><?php echo htmlspecialchars($course['title']); ?></h1>
            <div class="course-meta-top" style="display: flex; gap: 20px; color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">
                <span><i class="far fa-bookmark"></i> Wishlist</span>
                <span><i class="fas fa-share-alt"></i> Share</span>
            </div>
        </div>

        <div class="course-layout-grid">
            <div class="course-content-left">
                <div class="course-main-banner">
                    <img src="<?php echo htmlspecialchars($course['image_path']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>">
                </div>

                <div class="content-section">
                    <h3>What Will You Learn?</h3>
                    <div class="learning-outcomes">
                        <div class="outcome-item"><i class="fas fa-check"></i> We Provide the Detailed Web Development course</div>
                        <div class="outcome-item"><i class="fas fa-check"></i> Course contain 150+ hours of premium content</div>
                        <div class="outcome-item"><i class="fas fa-check"></i> We provide lifetime access with updates</div>
                        <div class="outcome-item"><i class="fas fa-check"></i> Client-oriented freelance concepts</div>
                    </div>
                </div>

                <div class="content-section">
                    <h3>Course Content</h3>
                    <div class="curriculum-container">
                        <?php
                        // Fetch all lessons for this course, ordered by your custom sort
                        $lesson_stmt = $conn->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY sort_order ASC");
                        $lesson_stmt->bind_param("i", $course_id);
                        $lesson_stmt->execute();
                        $lessons_res = $lesson_stmt->get_result();

                        $current_section = "";
                        while ($lesson = $lessons_res->fetch_assoc()): 
                            // Only show a new section header when the section name changes
                            if ($current_section !== $lesson['section_name']): 
                                $current_section = $lesson['section_name'];
                        ?>
                                <div class="curriculum-header">
                                    <span><?php echo htmlspecialchars($current_section); ?></span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            <?php endif; ?>

                            <div class="curriculum-body">
                                <div class="lesson-item <?php echo ($has_access || $lesson['is_preview']) ? 'unlocked' : 'locked'; ?>">
                                    <div class="lesson-info">
                                        <i class="fab fa-youtube"></i>
                                        <?php if ($has_access || $lesson['is_preview']): ?>
                                            <a href="watch.php?course=<?php echo $course_id; ?>&lesson=<?php echo $lesson['id']; ?>" style="text-decoration: none; color: #10b981; font-weight: 500;">
                                                <?php echo htmlspecialchars($lesson['lesson_title']); ?>
                                            </a>
                                        <?php else: ?>
                                            <span style="color: #64748b;"><?php echo htmlspecialchars($lesson['lesson_title']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="lesson-meta">
                                        <span class="duration"><?php echo htmlspecialchars($lesson['duration']); ?></span>
                                        <span class="lock-icon">
                                            <?php if ($has_access || $lesson['is_preview']): ?>
                                                <i class="fas fa-play-circle" style="color: #10b981;"></i>
                                            <?php else: ?>
                                                <i class="fas fa-lock" style="color: #94a3b8;"></i>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>

                        <?php if ($has_access): ?>
    <div class="quiz-invitation mt-4 p-4 text-center border rounded bg-light" style="border: 2px dashed #10b981 !important;">
        <h5 class="fw-bold text-dark">Ready to test your knowledge?</h5>
        <p class="small text-muted">Complete the assessment without leaving this page.</p>
        <button type="button" class="btn btn-success px-5 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#takeQuizModal">
            <i class="fas fa-pencil-alt me-2"></i> Start Course Quiz
        </button>
    </div>
<?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="course-sidebar-right">
                <div class="price-sticky-card">
                    <div class="price-tag">
                        <span class="symbol">₹</span><?php echo number_format($course['new_price'], 2); ?>
                    </div>

                    <?php if ($has_paid): ?>
                        <a href="learning-dashboard.php" class="btn-cart-action view-cart" style="background: #10b981;">
                            <i class="fas fa-play-circle"></i> Start Learning
                        </a>
                    <?php elseif ($is_in_cart): ?>
                        <a href="cart.php" class="btn-cart-action view-cart">
                            <i class="fas fa-shopping-bag"></i> View Cart
                        </a>
                    <?php else: ?>
                        <form action="courses.php" method="POST">
                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                            <input type="hidden" name="course_name" value="<?php echo htmlspecialchars($course['title']); ?>">
                            <input type="hidden" name="course_price" value="<?php echo $course['new_price']; ?>">
                            <button type="submit" name="add_to_course_cart" class="btn-cart-action add-cart">
                                Add to Cart
                            </button>
                        </form>
                    <?php endif; ?>

                    <p class="money-back">30-Day Money-Back Guarantee</p>

                    <div class="course-quick-specs" style="margin-top: 25px; display: grid; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                        <div class="spec-item" style="color: #475569; font-size: 0.95rem;"><i class="fas fa-signal" style="width: 25px; color: #6366f1;"></i> Beginner</div>
                        <div class="spec-item" style="color: #475569; font-size: 0.95rem;"><i class="far fa-clock" style="width: 25px; color: #6366f1;"></i> <?php echo htmlspecialchars($course['duration']); ?> Duration</div>
                        <div class="spec-item" style="color: #475569; font-size: 0.95rem;"><i class="fas fa-sync" style="width: 25px; color: #6366f1;"></i> Lifetime Access</div>
                    </div>

                    <div class="course-creator" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                        <p style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 10px;">A course by</p>
                        <div class="creator-details" style="display: flex; align-items: center; gap: 12px;">
                            <span class="creator-logo" style="background: #10b981; color: white; padding: 5px 8px; border-radius: 50%; font-weight: bold; font-size: 0.75rem;">AS</span>
                            <strong style="color: #1e293b;"><?php echo htmlspecialchars($course['instructor_name']); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="takeQuizModal" tabindex="-1" aria-labelledby="takeQuizModalLabel" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"> <div class="modal-content" style="border-radius: 15px; border: none; max-height: 90vh;"> <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="takeQuizModalLabel">
                    <i class="fas fa-clipboard-check me-2"></i> Quiz: <?php echo htmlspecialchars($course['title']); ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="auth.php" method="POST" style="overflow-y: auto;"> <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                
                <div class="modal-body p-4 bg-light" style="overflow-y: auto; max-height: 60vh;"> <?php 
                    $questions_res = $conn->query("SELECT * FROM quizzes WHERE course_id = $course_id");
                    if ($questions_res->num_rows > 0):
                        $count = 1;
                        while ($q = $questions_res->fetch_assoc()): 
                    ?>
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <p class="fw-bold mb-3"><?php echo $count++; ?>. <?php echo htmlspecialchars($q['question']); ?></p>
                                <div class="options-list d-grid gap-2">
                                    <?php foreach(['A'=>'option_a','B'=>'option_b','C'=>'option_c','D'=>'option_d'] as $key => $col): ?>
                                    <label class="p-3 border rounded cursor-pointer option-hover">
                                        <input type="radio" name="answer_<?php echo $q['id']; ?>" value="<?php echo $key; ?>" required class="me-2"> 
                                        <?php echo htmlspecialchars($q[$col]); ?>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else:
                        echo "<div class='text-center py-5'><p>No questions found.</p></div>";
                    endif;
                    ?>
                </div>

                <div class="modal-footer bg-white shadow-top">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <?php if ($questions_res->num_rows > 0): ?>
                        <button type="submit" name="submit_quiz" class="btn btn-success px-5 fw-bold">Submit Assessment</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; transition: 0.2s; }
    .cursor-pointer:hover { background-color: #f0fff4; border-color: #10b981 !important; }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quizBtn = document.querySelector('[data-bs-target="#takeQuizModal"]');
    if (!quizBtn) {
        console.error("Quiz button not found!");
    } else {
        console.log("Quiz button found and ready.");
    }
});
</script>
<?php require_once 'includes/footer.php'; ?>