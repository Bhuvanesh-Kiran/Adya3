<?php
require_once 'includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 
$is_logged_in = isset($_SESSION['user_id']); 

// If a user is logged in, but we are viewing the login box, we might have a pathing error
if ($is_logged_in && !isset($_GET['view'])) {
    // Force the dashboard view if logged in but no view is set
    header("Location: /learning-dashboard.php?view=dashboard");
    exit();
}
$page_title = 'Learning Dashboard';
require_once 'includes/header.php';

$error = isset($_GET['error']) ? $_GET['error'] : '';
$signup = isset($_GET['signup']) ? $_GET['signup'] : '';
$active_view = isset($_GET['view']) ? $_GET['view'] : 'dashboard';

$is_logged_in = isset($_SESSION['user_id']); 

// Initialize variables with defaults to prevent crashes
$user_data = [];
$reg_date = "N/A";
$enrolled_count = 0;
$displayName = "User";
$order_history_res = null;

// Data Fetching logic for when the user is successfully logged in
if ($is_logged_in) {
    try {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();
        
       // Data Fetching logic in learning-dashboard.php
if ($user_data) {
    // CHANGE: Priority should be the database 'first_name' column
    if (!empty($user_data['first_name'])) {
        $displayName = $user_data['first_name'] . ' ' . $user_data['last_name'];
    } else {
        $displayName = $_SESSION['full_name'] ?? $_SESSION['username'];
    }
    $reg_date = isset($user_data['created_at']) ? date("F j, Y g:i a", strtotime($user_data['created_at'])) : "N/A";
}

        // Fetch Enrollment Statistics
        $enroll_query = $conn->prepare("SELECT COUNT(*) as total FROM enrollments WHERE user_id = ? AND payment_status = 'completed'");
        $enroll_query->bind_param("i", $user_id);
        $enroll_query->execute();
        $enrolled_count = $enroll_query->get_result()->fetch_assoc()['total'] ?? 0;

        // Fetch Data for Enrolled View
        $enrolled_stmt = $conn->prepare("SELECT c.* FROM courses c JOIN enrollments e ON c.id = e.course_id WHERE e.user_id = ? AND e.payment_status = 'completed'");
        $enrolled_stmt->bind_param("i", $user_id);
        $enrolled_stmt->execute();
        $enrolled_courses_res = $enrolled_stmt->get_result();

        // Fetch Data for History View
        // CHANGE: Change e.created_at to e.enrolled_at
        $history_stmt = $conn->prepare("SELECT e.id as order_id, c.title, e.enrolled_at as date, c.new_price as price 
                                        FROM enrollments e 
                                        JOIN courses c ON e.course_id = c.id 
                                        WHERE e.user_id = ? AND e.payment_status = 'completed' 
                                        ORDER BY e.enrolled_at DESC");
        $history_stmt->bind_param("i", $user_id);
        $history_stmt->execute();
        $order_history_res = $history_stmt->get_result();

    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}
?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Learning Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Learning Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>
</section>

<?php if (!$is_logged_in): ?>
<div class="login-required">
    <div class="container">
        <div class="login-box" id="loginBox" style="display: <?php echo ($error == 'password_mismatch' || $error == 'weak_password' || $signup == 'success') ? 'none' : 'block'; ?>;">
            <div class="login-icon"><i class="fas fa-user-lock"></i></div>
            <h2>Login to Your Account</h2>
            <?php if ($error && ($error == 'user_not_found' || $error == 'invalid_password')): ?>
                <div class="alert-box-internal" style="color: var(--danger); font-size: 0.9rem; margin-bottom: 15px;">
                    <i class="fas fa-exclamation-circle"></i> 
                    <?php echo ($error == 'user_not_found') ? "No account found." : "Incorrect password."; ?>
                </div>
            <?php endif; ?>
            <form class="login-form" action="auth.php" method="POST">
                <div class="form-group mb-3"><label>Username or Email</label><input type="text" name="identifier" class="form-control" required></div>
                <div class="form-group mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                <button type="submit" name="login" class="btn btn-primary btn-block w-100">Login Now</button>
            </form>
            <div class="register-link mt-3">Don't have an account? <a href="javascript:void(0)" onclick="toggleAuth()">Register Now</a></div>
        </div>

        <div class="login-box" id="registerBox" style="display: <?php echo ($error == 'password_mismatch' || $error == 'weak_password') ? 'block' : 'none'; ?>;">
            <div class="login-icon"><i class="fas fa-user-plus"></i></div>
            <h2>Create Account</h2>
            <?php if ($error == 'password_mismatch'): ?>
        <div class="alert-box-internal" style="color: #ef4444; background: #fee2e2; padding: 10px; border-radius: 8px; font-size: 0.9rem; margin-bottom: 15px; border: 1px solid #fecaca;">
            <i class="fas fa-exclamation-circle"></i> Passwords do not match.Please try again
        </div>
<?php endif; ?>
            <form class="login-form" action="auth.php" method="POST">
                <div class="form-row mb-3">
                    <div class="form-group"><label>First Name</label><input type="text" name="first_name" class="form-control" required></div>
                    <div class="form-group"><label>Last Name</label><input type="text" name="last_name" class="form-control" required></div>
                </div>
                <div class="form-group mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div>
                <div class="form-group mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="form-group mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required 
                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}" 
                   title="Must contain at least 8 characters, including uppercase, lowercase, numbers, and special characters.">
            </div>
        
        <div class="form-group mb-3">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>

        <div class="checkbox-label mb-4" style="display: flex; align-items: center; gap: 10px; font-size: 0.9rem;">
            <input type="checkbox" name="terms" required style="width: auto;"> 
            <span>I agree to the <a href="terms-conditions.php" target="_blank" style="color: var(--success); font-weight: 600;">Terms and Conditions</a></span>
        </div>
                <button type="submit" name="register" class="btn btn-primary btn-block w-100">Register Now</button>
            </form>
            <div class="register-link mt-3">Already have an account? <a href="javascript:void(0)" onclick="toggleAuth()">Login Now</a></div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="dashboard-container-new">
    <div class="container">
        <div class="user-welcome-header">
            <div class="user-avatar-large">
                <?php 
                    $initials = "";
                    $parts = explode(' ', $displayName);
                    foreach($parts as $p) { if(!empty($p)) $initials .= substr($p, 0, 1); }
                    echo strtoupper(substr($initials, 0, 2));
                ?>
            </div>
            <div class="user-text">
                <p>Hello,</p>
                <h2><?php echo htmlspecialchars($displayName); ?></h2>
            </div>
        </div>

        <hr class="header-divider">

        <div class="dashboard-main-layout row">
            <aside class="col-lg-3 sidebar-nav-container">
                <nav class="side-menu">
                    <a href="learning-dashboard.php?view=dashboard" class="menu-item list-group-item <?php echo ($active_view == 'dashboard') ? 'active' : ''; ?>"><i class="fas fa-th-large"></i> Dashboard</a>
                    <a href="learning-dashboard.php?view=profile" class="menu-item list-group-item <?php echo ($active_view == 'profile') ? 'active' : ''; ?>"><i class="fas fa-user"></i> My Profile</a>
                    <a href="learning-dashboard.php?view=enrolled" class="menu-item list-group-item <?php echo ($active_view == 'enrolled') ? 'active' : ''; ?>"><i class="fas fa-graduation-cap"></i> Enrolled Courses</a>
                    <a href="learning-dashboard.php?view=wishlist" class="menu-item <?php echo ($active_view == 'wishlist') ? 'active' : ''; ?>">
            <i class="fas fa-bookmark"></i> Wishlist
        </a>
        <a href="learning-dashboard.php?view=quiz" class="menu-item <?php echo ($active_view == 'quiz') ? 'active' : ''; ?>">
            <i class="fas fa-tasks"></i> My Quiz Attempts
        </a>
        
        <a href="learning-dashboard.php?view=history" class="menu-item <?php echo ($active_view == 'history') ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Order History
        </a>
        
        <a href="learning-dashboard.php?view=qa" class="menu-item <?php echo ($active_view == 'qa') ? 'active' : ''; ?>">
            <i class="fas fa-question-circle"></i> Queries
        </a>

        <hr class="sidebar-divider" style="opacity: 0.1; margin: 10px 0;">
        
        <a href="learning-dashboard.php?view=settings" class="menu-item <?php echo ($active_view == 'settings') ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i> Settings
        </a>
        <a href="auth.php?logout=true" class="menu-item logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
                </nav>
            </aside>

            <main class="col-lg-9 content-display-area">
    <?php switch($active_view): 
        case 'dashboard': ?>
            <h3 class="section-title mb-4">Dashboard</h3>
            <div class="row g-4 d-flex flex-row">
                <div class="col-md-4"><div class="stat-box icon-enrolled"><div class="stat-icon"><i class="fas fa-book-open"></i></div><div class="stat-num"><?php echo $enrolled_count; ?></div><div class="stat-txt">Enrolled Courses</div></div></div>
                <div class="col-md-4"><div class="stat-box icon-active"><div class="stat-icon"><i class="fas fa-graduation-cap"></i></div><div class="stat-num">0</div><div class="stat-txt">Active Courses</div></div></div>
                <div class="col-md-4"><div class="stat-box icon-completed"><div class="stat-icon"><i class="fas fa-trophy"></i></div><div class="stat-num">0</div><div class="stat-txt">Completed Courses</div></div></div>
            </div>
        <?php break; ?>

        <?php case 'profile': ?>
            <h3 class="section-title mb-4">My Profile</h3>
            <div class="profile-card bg-white border rounded p-4 shadow-sm">
                <div class="p-row"><div class="p-label">Registration Date</div><div class="p-value"><?php echo $reg_date; ?></div></div>
                <div class="p-row"><div class="p-label">First Name</div><div class="p-value"><?php echo htmlspecialchars(explode(' ', $displayName)[0]); ?></div></div>
                <div class="p-row"><div class="p-label">Last Name</div><div class="p-value"><?php echo htmlspecialchars(explode(' ', $displayName, 2)[1] ?? '-'); ?></div></div>
                <div class="p-row"><div class="p-label">Username</div><div class="p-value"><?php echo htmlspecialchars($user_data['username'] ?? '-'); ?></div></div>
                <div class="p-row"><div class="p-label">Email</div><div class="p-value"><?php echo htmlspecialchars($user_data['email'] ?? '-'); ?></div></div>
                <div class="p-row"><div class="p-label">Phone Number</div><div class="p-value"><?php echo htmlspecialchars($user_data['phone'] ?? '-'); ?></div></div>
                <div class="p-row"><div class="p-label">Skill/Occupation</div><div class="p-value"><?php echo htmlspecialchars($user_data['occupation'] ?? '-'); ?></div></div>
                <div class="p-row" style="border-bottom: none;"><div class="p-label">Biography</div><div class="p-value"><?php echo htmlspecialchars($user_data['bio'] ?? '-'); ?></div></div>
            </div>
        <?php break; ?>

        <?php case 'enrolled': ?>
    <h3 class="section-title mb-4">Enrolled Courses</h3>
    <div class="row g-4">
        <?php 
        // Freshly fetch to ensure it uses the correct data
        $enrolled_stmt = $conn->prepare("SELECT c.* FROM courses c JOIN enrollments e ON c.id = e.course_id WHERE e.user_id = ? AND e.payment_status = 'completed'");
        $enrolled_stmt->bind_param("i", $user_id);
        $enrolled_stmt->execute();
        $enrolled_courses_res = $enrolled_stmt->get_result();

        if ($enrolled_courses_res->num_rows > 0): 
            while($course = $enrolled_courses_res->fetch_assoc()): ?>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 12px;">
                    <img src="<?php echo htmlspecialchars($course['image_path']); ?>" class="card-img-top" style="height: 160px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><?php echo htmlspecialchars($course['title']); ?></h5>
                        <div class="d-flex gap-2">
                            <a href="course-details.php?id=<?php echo $course['id']; ?>" class="btn btn-primary btn-sm flex-grow-1">Continue Learning</a>
                            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#takeQuizModal"><i class="fas fa-pencil-alt"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">No enrolled courses found.</p>
            </div>
        <?php endif; ?>
    </div>
<?php break; ?>

        <?php case 'quiz': ?>
            <h3 class="section-title mb-4">My Quiz Attempts</h3>
            <div class="table-responsive bg-white border rounded p-3 shadow-sm">
                <table class="table align-middle">
                    <thead class="bg-light">
                        <tr><th>Course Name</th><th>Score</th><th>Result</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $qa_stmt = $conn->prepare("SELECT qa.*, c.title FROM quiz_attempts qa JOIN courses c ON qa.course_id = c.id WHERE qa.user_id = ? ORDER BY qa.attempted_at DESC");
                        $qa_stmt->bind_param("i", $user_id);
                        $qa_stmt->execute();
                        $qa_res = $qa_stmt->get_result();
                        
                        if ($qa_res->num_rows > 0):
                            while($q = $qa_res->fetch_assoc()):
                                $pct = ($q['score'] / $q['total_questions']) * 100;
                        ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($q['title']); ?></td>
                                <td><?php echo $q['score']; ?>/<?php echo $q['total_questions']; ?> (<?php echo round($pct); ?>%)</td>
                                <td>
                                    <span class="badge <?php echo ($pct >= 70) ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo ($pct >= 70) ? 'PASSED' : 'FAILED'; ?>
                                    </span>
                                </td>
                                <td><?php echo date("M d, Y", strtotime($q['attempted_at'])); ?></td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="4" class="text-center py-4">No attempts found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php break; ?>

        <?php case 'history': ?>
    <h3 class="section-title mb-4">Order History</h3>
    <div class="table-responsive bg-white border rounded p-3 shadow-sm">
        <table class="table align-middle">
            <thead class="bg-light"><tr><th>Order ID</th><th>Name</th><th>Date</th><th>Price</th><th>Status</th></tr></thead>
            <tbody>
                <?php if ($order_history_res && $order_history_res->num_rows > 0): ?>
                    <?php while($h = $order_history_res->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo 1600 + $h['order_id']; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($h['title']); ?></td>
                            <td><?php echo date("F j, Y", strtotime($h['date'])); ?></td>
                            <td>₹<?php echo number_format($h['price'], 2); ?></td>
                            <td><span class="badge bg-success">Completed</span></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-4">No order history found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php break; ?>
        <?php case 'qa': ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="section-title mb-0">Question & Answer</h3>
        <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="collapse" data-bs-target="#askQuestionForm">
            <i class="fas fa-plus me-1"></i> Ask a New Question
        </button>
    </div>

    <div class="collapse mb-4" id="askQuestionForm">
        <div class="card card-body border-0 shadow-sm" style="border-radius: 15px;">
            <form action="auth.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Select Course</label>
                    <select name="course_id" class="form-select" required>
                        <option value="">-- Choose a course to ask about --</option>
                        <?php 
                        // Only show courses the student is actually enrolled in
                        $courses_query = $conn->query("SELECT c.id, c.title FROM courses c JOIN enrollments e ON c.id = e.course_id WHERE e.user_id = $user_id AND e.payment_status = 'completed'");
                        while($c = $courses_query->fetch_assoc()): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['title']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Your Question</label>
                    <textarea name="question" class="form-control" rows="3" placeholder="What would you like to know?" required></textarea>
                </div>
                <button type="submit" name="submit_question" class="btn btn-success px-4">Post Question</button>
            </form>
        </div>
    </div>

    <div class="qa-history">
        <?php 
        $qa_res = $conn->query("SELECT q.*, c.title FROM course_qa q JOIN courses c ON q.course_id = c.id WHERE q.user_id = $user_id ORDER BY q.created_at DESC");
        if ($qa_res->num_rows > 0):
            while($qa = $qa_res->fetch_assoc()):
        ?>
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($qa['title']); ?></span>
                        <small class="text-muted"><?php echo date('M d, Y', strtotime($qa['created_at'])); ?></small>
                    </div>
                    <p class="mt-3 fw-bold mb-0 text-dark">Q: <?php echo htmlspecialchars($qa['question']); ?></p>
                </div>
                
                <div class="card-footer py-3" style="background-color: <?php echo $qa['answer'] ? '#f0fff4' : '#fff9f0'; ?>; border-top: 1px solid rgba(0,0,0,0.05);">
                    <?php if($qa['answer']): ?>
                        <div class="d-flex gap-2">
                            <i class="fas fa-reply text-success mt-1"></i>
                            <div>
                                <small class="fw-bold text-success d-block mb-1">Instructor's Response:</small>
                                <p class="mb-0 text-dark small"><?php echo htmlspecialchars($qa['answer']); ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="mb-0 text-muted small italic"><i class="far fa-clock me-1"></i> Waiting for instructor's response...</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; else: ?>
            <div class="text-center py-5 bg-white rounded border">
                <p class="text-muted">You haven't asked any questions yet.</p>
            </div>
        <?php endif; ?>
    </div>
<?php break; ?>
        <?php case 'settings': ?>
    <h3 class="section-title mb-4">Settings</h3>
    <form action="auth.php" method="POST" class="settings-form bg-white border rounded p-4 shadow-sm">
        
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label text-muted small">First Name</label>
                <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user_data['first_name'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Last Name</label>
                <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user_data['last_name'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">User Name</label>
                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user_data['username'] ?? ''); ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Skill/Occupation</label>
                <input type="text" name="occupation" class="form-control" value="<?php echo htmlspecialchars($user_data['occupation'] ?? ''); ?>">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label text-muted small">Bio</label>
            <textarea name="bio" class="form-control" rows="6"><?php echo htmlspecialchars($user_data['bio'] ?? ''); ?></textarea>
        </div>

        <button type="submit" name="update_profile" class="btn btn-success px-4" style="background-color: #10b981; border: none;">Update Profile</button>
    </form>
<?php break; ?>

        <?php default: ?>
            <h3 class="section-title mb-4">
                <?php 
                    $titles = ['enrolled' => 'Enrolled Courses', 'wishlist' => 'Wishlist', 'quiz' => 'My Quiz Attempts', 'qa' => 'Question & Answer'];
                    echo $titles[$active_view] ?? 'Section';
                ?>
            </h3>
            <div class="text-center py-5 bg-white border rounded shadow-sm">
                <img src="assets/images/empty-mailbox.svg" style="width: 250px; opacity: 0.8;" alt="No Data">
                <p class="mt-4 text-muted h5">No Data Available in this Section</p>
            </div>
        <?php break; ?>
    <?php endswitch; ?>
</main>
        </div>
    </div>
</div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleAuth() {
    const login = document.getElementById('loginBox');
    const register = document.getElementById('registerBox');
    if (login.style.display === 'none') {
        login.style.display = 'block';
        register.style.display = 'none';
    } else {
        login.style.display = 'none';
        register.style.display = 'block';
    }
}
// Auto-scroll to data section on mobile when a view is active
document.addEventListener("DOMContentLoaded", function() {
    const params = new URLSearchParams(window.location.search);
    // Only scroll if a 'view' is set and we are on a mobile/tablet screen
    if (params.has('view') && window.innerWidth < 992) {
        const target = document.querySelector('.content-display-area');
        if (target) {
            // Smooth scroll to the content title
            const offset = 100; // Account for sticky header height
            const bodyRect = document.body.getBoundingClientRect().top;
            const elementRect = target.getBoundingClientRect().top;
            const elementPosition = elementRect - bodyRect;
            const offsetPosition = elementPosition - offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    }
});

</script>

<?php require_once 'includes/footer.php'; ?>