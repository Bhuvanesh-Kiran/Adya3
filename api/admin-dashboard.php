<?php
require_once 'includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Guard: Kick out anyone who isn't an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: learning-dashboard.php?error=access_denied");
    exit();
}

$active_view = isset($_GET['view']) ? $_GET['view'] : 'overview';
$page_title = 'Admin Panel';
require_once 'includes/header.php';
?>

<!-- Load Bootstrap CSS if not already loaded -->
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Admin Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Admin Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>
</section>

<div class="admin-dashboard-wrapper">
    <div class="container">
        <div class="admin-user-welcome">
            <div class="admin-avatar">AD</div>
            <div class="admin-welcome-text">
                <p>Administrator Portal</p>
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h2>
            </div>
        </div>

        <div class="admin-content-layout">
            <aside class="admin-sidebar">
                <nav>
                    <a href="admin-dashboard.php?view=overview" class="admin-menu-item <?php echo ($active_view == 'overview') ? 'active' : ''; ?>">
                        <i class="fas fa-chart-line"></i>
                        <span>Overview</span>
                    </a>
                    <a href="admin-dashboard.php?view=manage_courses" class="admin-menu-item <?php echo ($active_view == 'manage_courses') ? 'active' : ''; ?>">
                        <i class="fas fa-book"></i>
                        <span>Manage Courses</span>
                    </a>
                    <a href="admin-dashboard.php?view=manage_content" class="admin-menu-item <?php echo ($active_view == 'manage_content') ? 'active' : ''; ?>">
                        <i class="fas fa-file-video"></i>
                        <span>Course Content</span>
                    </a>
                    <a href="admin-dashboard.php?view=manage_quizzes" class="admin-menu-item <?php echo ($active_view == 'manage_quizzes') ? 'active' : ''; ?>">
                        <i class="fas fa-tasks"></i>
                        <span>Quiz Management</span>
                    </a>
                    <a href="admin-dashboard.php?view=qa_moderation" class="admin-menu-item <?php echo ($active_view == 'qa_moderation') ? 'active' : ''; ?>">
                        <i class="fas fa-comments"></i>
                        <span>Q&A Moderation</span>
                    </a>
                    <a href="admin-dashboard.php?view=course_insights" class="admin-menu-item <?php echo ($active_view == 'course_insights') ? 'active' : ''; ?>">
                        <i class="fas fa-chart-pie"></i>
                        <span>Course Insights</span>
                    </a>
                    <a href="admin-dashboard.php?view=inbox" class="admin-menu-item <?php echo ($active_view == 'inbox') ? 'active' : ''; ?>">
                        <i class="fas fa-envelope-open-text"></i>
                        <span>Contact Inbox</span>
                    </a>

                    <a href="auth.php?logout=true" class="admin-menu-item logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </nav>
            </aside>

            <main class="admin-main-content">
                <?php 
                switch($active_view) {
                    case 'overview': 
                        $user_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'student'")->fetch_assoc()['total'];
                        $course_count = $conn->query("SELECT COUNT(*) as total FROM courses")->fetch_assoc()['total'];
                        $inquiry_count = $conn->query("SELECT COUNT(*) as total FROM contact_requests WHERE status = 'new'")->fetch_assoc()['total'];
                        ?>
                        <h3 class="section-title mb-4">Platform Overview</h3>
                        <div class="row g-4 mb-5">
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm text-center p-3 stat-card" style="border-left: 5px solid #6366f1 !important;">
                                    <div class="card-body">
                                        <div class="icon-box mb-2" style="color: #6366f1; font-size: 2rem;">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                        <h5 class="text-muted small text-uppercase mb-2">Total Students</h5>
                                        <h2 class="fw-bold mb-0" style="color: #1e293b;"><?php echo $user_count; ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm text-center p-3 stat-card" style="border-left: 5px solid #10b981 !important;">
                                    <div class="card-body">
                                        <div class="icon-box mb-2" style="color: #10b981; font-size: 2rem;">
                                            <i class="fas fa-book-open"></i>
                                        </div>
                                        <h5 class="text-muted small text-uppercase mb-2">Active Courses</h5>
                                        <h2 class="fw-bold mb-0" style="color: #1e293b;"><?php echo $course_count; ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm text-center p-3 stat-card" style="border-left: 5px solid #f59e0b !important;">
                                    <div class="card-body">
                                        <div class="icon-box mb-2" style="color: #f59e0b; font-size: 2rem;">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <h5 class="text-muted small text-uppercase mb-2">New Inquiries</h5>
                                        <h2 class="fw-bold mb-0" style="color: #1e293b;"><?php echo $inquiry_count; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                        break; 

                    case 'manage_courses':
                        $courses_res = $conn->query("SELECT * FROM courses ORDER BY id DESC");
                        ?>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="section-title mb-0">Manage Courses</h3>
                            <button type="button" class="btn btn-success" onclick="openCourseModal()" style="padding: 10px 24px; font-weight: 500;">
                                <i class="fas fa-plus-circle me-2"></i>Add New Course
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Thumbnail</th>
                                        <th>Course Name</th>
                                        <th>Price</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($course = $courses_res->fetch_assoc()): ?>
                                    <tr>
                                        <td data-label="Thumbnail">
                                           <img src="<?php echo $course['image_path']; ?>" 
                                                 onerror="this.src='assets/images/placeholder.jpg';"
                                                 style="width: 70px; height: 45px; object-fit: cover; border-radius: 8px;">
                                        </td>
                                        <td data-label="Course Name"class="fw-bold" style="color: #1e293b;"><?php echo htmlspecialchars($course['title'] ?? 'Untitled'); ?></td>
                                        <td data-label="Price"style="color: #10b981; font-weight: 600;">
                                            <?php 
                                            $new_price = (float)($course['new_price'] ?? 0); 
                                            echo "₹" . number_format($new_price, 2); 
                                            ?>
                                        </td>
                                        <td data-label="Actions"class="text-center">
                                            <button class="btn btn-sm btn-outline-primary me-1" 
                                                    onclick='editCourse(<?php echo htmlspecialchars(json_encode($course), ENT_QUOTES, "UTF-8"); ?>)'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteCourse(<?php echo $course['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                        break;
                    case 'manage_content':
    $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
    $courses = $conn->query("SELECT id, title FROM courses ORDER BY title ASC");
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="section-title mb-0">Course Curriculum</h3>
        <?php if ($course_id > 0): ?>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addLessonModal">
                <i class="fas fa-plus-circle me-2"></i>Add New Lesson
            </button>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="admin-dashboard.php" class="row g-3 align-items-end">
                <input type="hidden" name="view" value="manage_content">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Select Course to Manage Content</label>
                    <select name="course_id" class="form-select" onchange="this.form.submit()">
                        <option value="0">-- Choose a Course --</option>
                        <?php while($c = $courses->fetch_assoc()): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ($course_id == $c['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['title']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <?php if ($course_id > 0): 
        $lessons = $conn->query("SELECT * FROM lessons WHERE course_id = $course_id ORDER BY sort_order ASC");
        ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Sort</th>
                        <th>Section Name</th>
                        <th>Lesson Title</th>
                        <th>Duration</th>
                        <th>Preview</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($lessons->num_rows > 0): ?>
                        <?php while($lesson = $lessons->fetch_assoc()): ?>
                        <tr>
                            <td data-label="Sort"><?php echo $lesson['sort_order']; ?></td>
                            <td data-label="Section name"><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($lesson['section_name']); ?></span></td>
                            <td data-label="Lesson title" class="fw-bold"><?php echo htmlspecialchars($lesson['lesson_title']); ?></td>
                            <td data-label="duration"><?php echo $lesson['duration']; ?></td>
                            <td data-label="preview"><?php echo $lesson['is_preview'] ? '<span class="text-success">Yes</span>' : '<span class="text-muted">No</span>'; ?></td>
                            <td data-label="actions" class="text-center">
                                <button class="btn btn-sm btn-outline-primary me-1" 
                                        onclick='editLesson(<?php echo htmlspecialchars(json_encode($lesson), ENT_QUOTES, "UTF-8"); ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteLesson(<?php echo $lesson['id']; ?>, <?php echo $course_id; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4">No lessons found for this course.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <?php
    break;
                    case 'manage_quizzes':
    $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
    $courses = $conn->query("SELECT id, title FROM courses ORDER BY title ASC");
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="section-title mb-0">Quiz Management</h3>
        <?php if ($course_id > 0): ?>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addQuizModal">
                <i class="fas fa-plus-circle me-2"></i>Add Question
            </button>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="admin-dashboard.php">
                <input type="hidden" name="view" value="manage_quizzes">
                <select name="course_id" class="form-select" onchange="this.form.submit()">
                    <option value="0">-- Select Course to View Quizzes --</option>
                    <?php while($c = $courses->fetch_assoc()): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ($course_id == $c['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['title']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>
    </div>

    <?php if ($course_id > 0): 
        $quizzes = $conn->query("SELECT * FROM quizzes WHERE course_id = $course_id");
        ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Question</th>
                        <th>Options</th>
                        <th>Correct</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($q = $quizzes->fetch_assoc()): ?>
                    <tr>
                        <td data-label="question"class="fw-bold"><?php echo htmlspecialchars($q['question']); ?></td>
                        <td data-label="options"class="small">A: <?php echo $q['option_a']; ?><br>B: <?php echo $q['option_b']; ?><br>C: <?php echo $q['option_c']; ?><br>D: <?php echo $q['option_d']; ?></td>
                        <td data-label="correct"><span class="badge bg-success"><?php echo $q['correct_option']; ?></span></td>
                        <td data-label="action"class="text-center">
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteQuiz(<?php echo $q['id']; ?>, <?php echo $course_id; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <?php
    break;
                    case 'qa_moderation':
    $qa_query = "SELECT q.*, c.title as course_name, u.first_name, u.last_name 
                 FROM course_qa q 
                 JOIN courses c ON q.course_id = c.id 
                 JOIN users u ON q.user_id = u.id 
                 ORDER BY q.created_at DESC";
    $qa_res = $conn->query($qa_query);
    ?>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="section-title mb-0">Q&A Moderation</h3>
        <span class="badge bg-primary"><?php echo $qa_res->num_rows; ?> Total Questions</span>
    </div>

    <div class="qa-container">
        <?php if ($qa_res->num_rows > 0): ?>
            <?php while($qa = $qa_res->fetch_assoc()): ?>
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <span class="badge bg-light text-primary border border-primary mb-2">
                                    Course: <?php echo htmlspecialchars($qa['course_name']); ?>
                                </span>
                                <h6 class="mb-0 fw-bold">
                                    <i class="fas fa-user-circle me-1"></i> 
                                    <?php echo htmlspecialchars($qa['first_name'] . ' ' . $qa['last_name']); ?>
                                </h6>
                            </div>
                            <small class="text-muted"><?php echo date('M d, Y', strtotime($qa['created_at'])); ?></small>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="question-box p-3 bg-light rounded-3 mb-3">
                            <p class="mb-0 fst-italic">"<?php echo htmlspecialchars($qa['question']); ?>"</p>
                        </div>

                        <form action="process-admin.php" method="POST">
                            <input type="hidden" name="qa_id" value="<?php echo $qa['id']; ?>">
                            <div class="form-group mb-3">
                                <label class="form-label small fw-bold text-success">
                                    <i class="fas fa-reply me-1"></i> Your Answer:
                                </label>
                                <textarea name="answer" class="form-control" rows="3" 
                                          placeholder="Type your response to the student..."><?php echo htmlspecialchars($qa['answer'] ?? ''); ?></textarea>
                            </div>

                            <!-- Status + Actions: always inside the card -->
                            <?php if($qa['answer']): ?>
                                <div class="text-success small mb-2 fw-semibold">
                                    <i class="fas fa-check-circle me-1"></i> Answered
                                </div>
                            <?php endif; ?>

                            <div class="d-flex gap-2">
                                <button type="submit" name="submit_answer" class="btn btn-primary btn-sm px-4 flex-grow-1">
                                    Save Response
                                </button>
                                <a href="process-admin.php?delete_qa=<?php echo $qa['id']; ?>" 
                                   class="btn btn-outline-danger btn-sm" 
                                   onclick="return confirm('Delete this question permanently?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center py-5 bg-white rounded-3 shadow-sm">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <p class="text-muted">No student questions found in the database.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php
    break;
    case 'course_insights':
    // Updated Query: Now includes counting the questions per course
    $insights_query = "SELECT 
                        c.id, 
                        c.title, 
                        c.category,
                        (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id AND e.payment_status = 'completed') as total_students,
                        (SELECT COUNT(DISTINCT user_id) FROM quiz_attempts qa WHERE qa.course_id = c.id) as quiz_participants,
                        (SELECT COUNT(*) FROM quizzes q WHERE q.course_id = c.id) as total_questions,
                        (SELECT MAX(score) FROM quiz_attempts qa WHERE qa.course_id = c.id) as top_score
                       FROM courses c 
                       ORDER BY total_students DESC";
    $insights_res = $conn->query($insights_query);
    ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="section-title mb-0">Course Performance Insights</h3>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-print me-2"></i>Print Report
        </button>
    </div>

    <div class="table-responsive bg-white rounded shadow-sm p-3">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Course Details</th>
                    <th class="text-center">Enrolled Students</th>
                    <th class="text-center">Quiz Takers</th>
                    <th class="text-center">Questions</th>
                    <th class="text-center">Engagement Rate</th>
                    <th class="text-center">Top Score</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($insights_res->num_rows > 0): ?>
                    <?php while($row = $insights_res->fetch_assoc()): 
                        // Safety Check: Prevent DivisionByZeroError
                        $total_students = (int)$row['total_students'];
                        $takers = (int)$row['quiz_participants'];
                        
                        $engagement = ($total_students > 0) ? round(($takers / $total_students) * 100) : 0;
                    ?>
                        <tr>
                            <td data-label="course-details">
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['title']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($row['category']); ?></small>
                            </td>
                            <td data-label="enrolled students"class="text-center">
                                <span class="badge bg-light text-primary border border-primary px-3 py-2">
                                    <?php echo $total_students; ?> Students
                                </span>
                            </td>
                            <td data-label="quiz takers"class="text-center"><?php echo $takers; ?></td>
                            <td data-label="questions"class="text-center text-muted"><?php echo $row['total_questions']; ?> Items</td>
                            <td data-label="engagement-rate"class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; max-width: 80px;">
                                        <div class="progress-bar bg-success" style="width: <?php echo $engagement; ?>%"></div>
                                    </div>
                                    <small class="fw-bold"><?php echo $engagement; ?>%</small>
                                </div>
                            </td>
                            <td data-label="top score"class="text-center text-success fw-bold">
                                <?php echo ($row['top_score'] !== null) ? $row['top_score'] . '/' . $row['total_questions'] : '-'; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">No courses found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
    break;
                    case 'inbox':
    $inbox_res = $conn->query("SELECT * FROM contact_requests ORDER BY created_at DESC");
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="section-title mb-0">Contact Inbox</h3>
        <span class="badge bg-secondary"><?php echo $inbox_res->num_rows; ?> Total Inquiries</span>
    </div>

    <div class="inbox-container">
        <?php if ($inbox_res->num_rows > 0): ?>
            <div class="table-responsive bg-white rounded shadow-sm p-3">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Status</th>
                            <th>Sender</th>
                            <th>Service Requested</th>
                            <th>Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($msg = $inbox_res->fetch_assoc()): ?>
                            <tr>
                                <td data-label="status">
                                    <?php if($msg['status'] == 'new'): ?>
                                        <span class="badge bg-danger">New</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted">Read</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="sender">
                                    <div class="fw-bold"><?php echo htmlspecialchars($msg['name'] ?? 'No Name'); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($msg['email'] ?? ''); ?></small>
                                </td>
                                <td data-label="service requested">
                                    <span class="badge bg-info text-dark" style="font-size: 0.75rem;">
                                        <?php echo htmlspecialchars($msg['service'] ?? 'General'); ?>
                                    </span>
                                </td>
                                <td data-label="date"><?php echo date('M d, Y', strtotime($msg['created_at'])); ?></td>
                                <td data-label="action"class="text-center">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick='viewMessage(<?php echo htmlspecialchars(json_encode($msg), ENT_QUOTES, "UTF-8"); ?>)'>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <p class="text-muted">No messages found.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php
    break;
                    
                    default:
                        ?>
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list" style="font-size: 4rem; color: #d1d5db; margin-bottom: 20px;"></i>
                            <h3 style="color: #6b7280;">Select an Option</h3>
                            <p style="color: #9ca3af;">Use the sidebar to manage your platform content.</p>
                        </div>
                        <?php
                }
                ?>
            </main>
        </div>
    </div>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none;">
                <h5 class="modal-title" style="font-weight: 600;">
                    <i class="fas fa-plus-circle me-2"></i>Create New Course
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeCourseModal()"></button>
            </div>
            <form action="process-admin.php" method="POST" enctype="multipart/form-data" id="addCourseForm">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold" style="color: #374151;">Course Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. Advanced PHP Development" style="padding: 10px; border-radius: 8px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="color: #374151;">Price (INR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="border-radius: 8px 0 0 8px;">₹</span>
                                <input type="number" name="price" class="form-control" required placeholder="0.00" step="0.01" min="0" style="padding: 10px; border-radius: 0 8px 8px 0;">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold" style="color: #374151;">Short Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Describe the course value and what students will learn..." style="padding: 10px; border-radius: 8px;"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #374151;">Thumbnail Image <span class="text-danger">*</span></label>
                            <input type="file" name="course_image" class="form-control" accept="image/*" required id="courseImage" onchange="previewImage(event)" style="padding: 10px; border-radius: 8px;">
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle"></i> Recommended: 800x600px, JPG or PNG
                            </small>
                            <div id="imagePreview" style="display: none; margin-top: 10px;">
                                <img id="previewImg" src="" alt="Preview" style="max-width: 100%; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid #e5e7eb;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #374151;">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required style="padding: 10px; border-radius: 8px;">
                                <option value="">-- Select Category --</option>
                                <option value="Web Development">Web Development</option>
                                <option value="Python">Python</option>
                                <option value="MERN Stack">MERN Stack</option>
                                <option value="Salesforce">Salesforce</option>
                                <option value="Mobile Development">Mobile Development</option>
                                <option value="Data Science">Data Science</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #374151;">Course Duration (e.g. 45h or 2 Months) <span class="text-danger">*</span></label>
                            <input type="text" name="duration" class="form-control" required placeholder="e.g. 45h" style="padding: 10px; border-radius: 8px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #f9fafb; border: none;">
                    <button type="button" class="btn btn-secondary" onclick="closeCourseModal()" style="padding: 10px 20px; border-radius: 8px;">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" name="add_course" class="btn btn-primary px-4" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; padding: 10px 24px; border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-save me-1"></i>Save Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header" style="background: #6366f1; color: white;">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Course</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="process-admin.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="course_id" id="edit_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Course Title</label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Price (INR)</label>
                            <input type="number" name="price" id="edit_price" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Category</label>
                            <select name="category" id="edit_category" class="form-select">
                                <option value="Web Development">Web Development</option>
                                <option value="Python">Python</option>
                                <option value="MERN Stack">MERN Stack</option>
                                <option value="Salesforce">Salesforce</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Duration</label>
                            <input type="text" name="duration" id="edit_duration" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">New Thumbnail (Optional)</label>
                            <input type="file" name="course_image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_course" class="btn btn-primary px-4">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addLessonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add Course Lesson</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="process-admin.php" method="POST">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Section Name (e.g. 1) Introduction)</label>
                        <input type="text" name="section_name" class="form-control" required placeholder="Section heading">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Lesson Title</label>
                        <input type="text" name="lesson_title" class="form-control" required placeholder="Video title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">YouTube Video URL</label>
                        <input type="text" name="video_url" class="form-control" required placeholder="Paste YouTube link here">
                        <small class="text-muted">Example: https://www.youtube.com/watch?v=dQw4w9WgXcQ</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Duration</label>
                            <input type="text" name="duration" class="form-control" placeholder="05:30">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="1">
                        </div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_preview" value="1">
                        <label class="form-check-label">Allow Free Preview?</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_lesson" class="btn btn-primary w-100">Save Lesson</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editLessonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Lesson Content</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="process-admin.php" method="POST">
                <input type="hidden" name="lesson_id" id="edit_lesson_id">
                <input type="hidden" name="course_id" id="edit_lesson_course_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Section Name</label>
                        <input type="text" name="section_name" id="edit_lesson_section" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Lesson Title</label>
                        <input type="text" name="lesson_title" id="edit_lesson_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">YouTube Video URL/ID</label>
                        <input type="text" name="video_url" id="edit_lesson_url" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Duration</label>
                            <input type="text" name="duration" id="edit_lesson_duration" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Sort Order</label>
                            <input type="number" name="sort_order" id="edit_lesson_sort" class="form-control">
                        </div>
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_preview" value="1" id="edit_lesson_preview">
                        <label class="form-check-label fw-bold" for="edit_lesson_preview">Allow Free Preview?</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_lesson" class="btn btn-primary px-4">Update Lesson</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addQuizModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-question-circle me-2"></i>Add Quiz Question</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="process-admin.php" method="POST">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Question Text</label>
                        <textarea name="question" class="form-control" rows="3" required placeholder="Enter the question here..."></textarea>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Option A</label>
                            <input type="text" name="opt_a" class="form-control" required placeholder="First choice">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Option B</label>
                            <input type="text" name="opt_b" class="form-control" required placeholder="Second choice">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Option C</label>
                            <input type="text" name="opt_c" class="form-control" required placeholder="Third choice">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Option D</label>
                            <input type="text" name="opt_d" class="form-control" required placeholder="Fourth choice">
                        </div>
                        
                        <div class="col-md-12 mt-4">
                            <label class="form-label fw-bold text-success">Select Correct Answer</label>
                            <select name="correct" class="form-select border-success" required>
                                <option value="">-- Choose Correct Option --</option>
                                <option value="A">Option A</option>
                                <option value="B">Option B</option>
                                <option value="C">Option C</option>
                                <option value="D">Option D</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_quiz" class="btn btn-success px-5">Save Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewMessageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-envelope-open me-2"></i>Inquiry Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="text-muted small d-block">Name</label>
                        <strong id="msg_sender"></strong>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small d-block">Phone</label>
                        <strong id="msg_phone"></strong>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Email</label>
                    <span id="msg_email"></span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Service Interested In</label>
                    <span class="badge bg-primary" id="msg_service"></span>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="text-muted small d-block">Message</label>
                    <p id="msg_content" class="bg-light p-3 rounded" style="white-space: pre-wrap; min-height: 100px;"></p>
                </div>
            </div>
            <div class="modal-footer">
                <form action="process-admin.php" method="POST">
                    <input type="hidden" name="msg_id" id="msg_id_input">
                    <button type="submit" name="mark_read" class="btn btn-success">Mark as Read</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Load Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let courseModal = new bootstrap.Modal(document.getElementById('addCourseModal'));
let editModal = new bootstrap.Modal(document.getElementById('editCourseModal'));
let editLessonModal = new bootstrap.Modal(document.getElementById('editLessonModal'));
let viewMsgModal = new bootstrap.Modal(document.getElementById('viewMessageModal'));

// Initialize modal when page loads
window.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('addCourseModal');
    if (modalElement && typeof bootstrap !== 'undefined') {
        courseModal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });
    }
});

// Function to open modal
function openCourseModal() {
    if (courseModal) {
        courseModal.show();
    } else {
        const modalElement = document.getElementById('addCourseModal');
        if (modalElement) {
            courseModal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });
            courseModal.show();
        }
    }
}

// Function to close modal
function closeCourseModal() {
    if (courseModal) {
        courseModal.hide();
    }
    document.getElementById('addCourseForm').reset();
    document.getElementById('imagePreview').style.display = 'none';
}

// Image preview function
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}
function editCourse(course) {
    document.getElementById('edit_id').value = course.id;
    document.getElementById('edit_title').value = course.title;
    document.getElementById('edit_price').value = course.new_price;
    document.getElementById('edit_category').value = course.category;
    document.getElementById('edit_duration').value = course.duration;
    editModal.show();
}

// Delete Function
function deleteCourse(id) {
    if(confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
        window.location.href = 'process-admin.php?delete_course=' + id;
    }
}
function editLesson(lesson) {
    document.getElementById('edit_lesson_id').value = lesson.id;
    document.getElementById('edit_lesson_course_id').value = lesson.course_id;
    document.getElementById('edit_lesson_section').value = lesson.section_name;
    document.getElementById('edit_lesson_title').value = lesson.lesson_title;
    document.getElementById('edit_lesson_url').value = lesson.video_url;
    document.getElementById('edit_lesson_duration').value = lesson.duration;
    document.getElementById('edit_lesson_sort').value = lesson.sort_order;
    
    // Handle checkbox
    document.getElementById('edit_lesson_preview').checked = (lesson.is_preview == 1);
    
    editLessonModal.show();
}

function deleteLesson(id, courseId) {
    if(confirm('Remove this lesson from the curriculum?')) {
        window.location.href = 'process-admin.php?delete_lesson=' + id + '&course_id=' + courseId;
    }
}

// Function to delete a quiz question
function deleteQuiz(id, courseId) {
    if(confirm('Are you sure you want to delete this question?')) {
        window.location.href = 'process-admin.php?delete_quiz=' + id + '&course_id=' + courseId;
    }
}

function viewMessage(msg) {
    document.getElementById('msg_id_input').value = msg.id;
    document.getElementById('msg_sender').innerText = msg.name; // Matches 'name'
    document.getElementById('msg_phone').innerText = msg.phone;   // Matches 'phone'
    document.getElementById('msg_email').innerText = msg.email;
    document.getElementById('msg_service').innerText = msg.service; // Matches 'service'
    document.getElementById('msg_content').innerText = msg.message;
    viewMsgModal.show();
}

// Precision Mobile Scroll Handler
// Auto-scroll to content section when a view is selected on mobile
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('view') && window.innerWidth < 992) {
        const target = document.querySelector('.admin-main-content');
        if (target) {
            const offset = 100; // Adjust for sticky header height
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