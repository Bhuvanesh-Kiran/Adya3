<?php
require_once 'includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 

$lesson_id = isset($_GET['lesson']) ? (int)$_GET['lesson'] : 0;
$course_id = isset($_GET['course']) ? (int)$_GET['course'] : 0;

// 1. Fetch Lesson and Video details
$stmt = $conn->prepare("SELECT * FROM lessons WHERE id = ? AND course_id = ?");
$stmt->bind_param("ii", $lesson_id, $course_id);
$stmt->execute();
$lesson = $stmt->get_result()->fetch_assoc();

if (!$lesson) { die("Lesson not found."); }

// 2. Security Check: Only Admin or Paid Users can watch (unless it's a preview)
$has_access = false;
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $has_access = true;
} elseif ($lesson['is_preview'] == 1) {
    $has_access = true;
} elseif (isset($_SESSION['user_id'])) {
    $check = $conn->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ? AND payment_status = 'completed'");
    $check->bind_param("ii", $_SESSION['user_id'], $course_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) { $has_access = true; }
}

if (!$has_access) { header("Location: course-details.php?id=$course_id&error=locked"); exit(); }

// 3. Helper function to get YouTube ID
function getYoutubeId($url) {
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
    return isset($match[1]) ? $match[1] : $url;
}

$vid_id = getYoutubeId($lesson['video_url']);
require_once 'includes/header.php';
?>
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Watch</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
                    <li class="breadcrumb-item"><a href="course-details.php">Course-details</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Watch</li>
                </ol>
            </nav>
        </div>
    </div>
</section>
<div class="video-player-section py-5 bg-dark">
    <div class="container text-center">
        <h2 class="text-white mb-4"><?php echo htmlspecialchars($lesson['lesson_title']); ?></h2>
        
        <div class="ratio ratio-16x9 mx-auto shadow-lg" style="max-width: 900px; border-radius: 12px; overflow: hidden;">
            <iframe src="https://www.youtube.com/embed/<?php echo $vid_id; ?>?rel=0&modestbranding=1" 
                    title="YouTube video player" frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen></iframe>
        </div>

        <div class="mt-4">
            <a href="course-details.php?id=<?php echo $course_id; ?>" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-2"></i> Back to Curriculum
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>